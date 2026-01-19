<?php

namespace App\Http\Controllers;
use App\Models\Project;
use App\Models\ProjectPhaseProgressLog;
use App\Models\ProjectMaterial;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\ProjectPhaseSchedule;
use App\Models\ProjectMaterialOut;


class ProjectReportController extends Controller
{
    // halaman pilih proyek (seperti pick progress / pick expenses)
    public function pickProject()
    {
        $projects = Project::with('client')
            ->orderByDesc('created_at')
            ->get();

        return view('report.pick', compact('projects'));
    }

    // cetak pdf 1 proyek
    public function projectPdf(Project $project)
    {
        // load relasi yang kamu butuhkan
        $project->load(['client','phases'=>fn($q)=>$q->orderBy('urutan'),'projectSdms.sdm']);


        // =========================
        // PROGRESS TOTAL (bobot * progress)
        // =========================
        $progressTotal = $project->phases->sum(function ($p) {
            return ($p->persen * ($p->progress ?? 0)) / 100;
        });

        $materials = ProjectMaterial::where('project_id', $project->id)
            ->withSum('stocks as qty_masuk_total', 'qty_masuk')
            ->orderBy('nama_material')
            ->get();

        // =========================
        // MATERIAL vs PENGELUARAN
        // =========================
        $materialEvaluations = [];
        $totalEstimasiNilai = 0;
        $totalRealisasiNilai = 0;

        foreach ($materials as $m) {
            $estimasiQty = (float) ($m->qty_estimasi ?? 0);
            $harga       = (float) ($m->harga ?? 0);
            $toleransi   = (float) ($m->toleransi_persen ?? 0);

            // realisasi = stok keluar
            $keluarQty = ProjectMaterialOut::where('project_id', $project->id)
                ->where('project_material_id', $m->id)
                ->sum('qty_keluar');

            $estimasiNilai  = $estimasiQty * $harga;
            $realisasiNilai = $keluarQty * $harga;

            $totalEstimasiNilai  += $estimasiNilai;
            $totalRealisasiNilai += $realisasiNilai;

            // batas atas toleransi
            $batasAtas = $estimasiQty * (1 + ($toleransi / 100));

            // evaluasi status
            if ($estimasiQty <= 0) {
                $status = 'Tidak Ada Estimasi';
                $badge  = 'badge-info';
            } elseif ($keluarQty > $batasAtas) {
                $status = 'Melebihi Estimasi';
                $badge  = 'badge-danger';
            } elseif ($keluarQty >= $estimasiQty) {
                $status = 'Sesuai (Habis)';
                $badge  = 'badge-warning';
            } else {
                $status = 'Sesuai';
                $badge  = 'badge-success';
            }

            $materialEvaluations[] = [
                'nama'            => $m->nama_material,
                'satuan'          => $m->satuan,
                'estimasi_qty'    => $estimasiQty,
                'keluar_qty'      => $keluarQty,
                'harga'           => $harga,
                'estimasi_nilai'  => $estimasiNilai,
                'realisasi_nilai' => $realisasiNilai,
                'status'          => $status,
                'badge'           => $badge,
            ];
        }


        // =========================
        // JADWAL vs PROGRESS REAL
        // =========================
        $today = date('Y-m-d'); // patokan evaluasi (tanggal cetak)

        // ambil jadwal per tahapan (key by project_phase_id)
        $schedules = ProjectPhaseSchedule::where('project_id', $project->id)
            ->get()
            ->keyBy('project_phase_id');

        // hitung planned per tahapan + planned total berbobot
        $plannedByPhase = [];
        $plannedTotal = 0;

        foreach ($project->phases as $ph) {
            $sch = $schedules->get($ph->id);

            $planned = null; // kalau tidak ada jadwal
            $jadwalText = '-';

            if ($sch) {
                $start = $sch->tanggal_mulai;
                $end   = $sch->tanggal_selesai;
                $jadwalText = $start . ' s/d ' . $end;

                // planned% berdasarkan timeline jadwal
                if ($today < $start) {
                    $planned = 0;
                } elseif ($today > $end) {
                    $planned = 100;
                } else {
                    // hari berjalan inklusif
                    $totalHari = (int) (date_diff(date_create($start), date_create($end))->days + 1);
                    $jalanHari = (int) (date_diff(date_create($start), date_create($today))->days + 1);

                    if ($totalHari < 1) $totalHari = 1;
                    if ($jalanHari < 0) $jalanHari = 0;
                    if ($jalanHari > $totalHari) $jalanHari = $totalHari;

                    $planned = round(($jalanHari / $totalHari) * 100, 1);
                }

                // planned total berbobot
                $plannedTotal += ((float)$ph->persen * (float)$planned) / 100;
            }

            $real = (float) ($ph->progress ?? 0);

            $diff = null;
            if (!is_null($planned)) {
                $diff = round($real - $planned, 1); // (+) lebih cepat, (-) terlambat
            }

            $plannedByPhase[] = [
                'nama_tahapan' => $ph->nama_tahapan,
                'bobot' => (float) $ph->persen,
                'jadwal' => $jadwalText,
                'planned' => $planned,
                'real' => round($real, 1),
                'diff' => $diff,
            ];
        }

        // status ringkas jadwal vs real (threshold bisa kamu atur)
        $plannedTotal = round($plannedTotal, 1);
        $progressTotalReal = round((float)$progressTotal, 1);

        $selisihTotal = null;
        $evaluasiJadwal = 'Belum ada jadwal';
        $badgeJadwal = 'badge-warning';

        if ($schedules->count() > 0) {
            $selisihTotal = round($progressTotalReal - $plannedTotal, 1);

            // ambang "sesuai" misal +/- 5%
            $threshold = 5;

            if (abs($selisihTotal) <= $threshold) {
                $evaluasiJadwal = 'Sesuai';
                $badgeJadwal = 'badge-success';
            } elseif ($selisihTotal < -$threshold) {
                $evaluasiJadwal = 'Terlambat';
                $badgeJadwal = 'badge-danger';
            } else {
                $evaluasiJadwal = 'Lebih cepat';
                $badgeJadwal = 'badge-info';
            }
        }



        // =========================
        // LOG PROGRESS + FOTO
        // =========================
        $logs = ProjectPhaseProgressLog::with(['phase', 'photos','sdms'])
            ->where('project_id', $project->id)
            ->latest('tanggal_update')
            ->latest()
            ->get();

        $sdmFromLogs = $logs->flatMap(function ($l) {
                return $l->sdms; // koleksi SDM per log
            })
            ->unique('id')
            ->values();
        

        // =========================
        // STATUS PROYEK (simple)
        // =========================
        $today = date('Y-m-d');
        $status = 'Aktif';

        if ($today < $project->tanggal_mulai) $status = 'Belum Mulai';
        if ($today > $project->tanggal_selesai) $status = 'Terlambat';
        if (round($progressTotal) >= 100) $status = 'Selesai';

        // ===== logo base64 (taruh file logo di public/images/logo-nkm.jpg) =====
        $logoPath = public_path('images/logo-nkm.jpeg'); // ganti sesuai nama file logo kamu
        $logoBase64 = null;

        if (file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $data = file_get_contents($logoPath);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        // generate PDF
        $pdf = Pdf::loadView('report.project_pdf', compact(
            'project',
            'progressTotal',
            'logs',
            'materials',
            'status',
            'logoBase64',
            'sdmFromLogs',

            'plannedByPhase',
            'plannedTotal',
            'progressTotalReal',
            'selisihTotal',
            'evaluasiJadwal',
            'badgeJadwal',

            'materialEvaluations',
            'totalEstimasiNilai',
            'totalRealisasiNilai',
        ))->setPaper('A4', 'landscape');

        return $pdf->download('Laporan-Proyek-' . $project->nama_proyek . '.pdf');
    }
    
}
