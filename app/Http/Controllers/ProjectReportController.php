<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\ProjectPhaseProgressLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

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
        $project->load([
            'client',
            'phases' => function ($q) {
                $q->orderBy('urutan');
            },
            'projectSdms.sdm',
        ]);

        // =========================
        // PROGRESS TOTAL (bobot * progress)
        // =========================
        $progressTotal = $project->phases->sum(function ($p) {
            return ($p->persen * ($p->progress ?? 0)) / 100;
        });

        // =========================
        // LOG PROGRESS + FOTO
        // =========================
        $logs = ProjectPhaseProgressLog::with(['phase', 'photos'])
            ->where('project_id', $project->id)
            ->latest('tanggal_update')
            ->latest()
            ->get();

        // =========================
        // TOTAL PENGELUARAN
        // =========================
        $totalPengeluaran = ProjectExpense::where('project_id', $project->id)->sum('nominal');

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
            'totalPengeluaran',
            'status',
            'logoBase64'
        ))->setPaper('A4', 'portrait');

        return $pdf->download('Laporan-Proyek-' . $project->nama_proyek . '.pdf');
    }
}
