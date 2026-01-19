<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\ProjectPhaseSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectScheduleGenerateController extends Controller
{
    public function form(Project $project)
    {
        $project->load(['phases' => function($q){
            $q->orderBy('urutan');
        }]);

        // total hari proyek (inklusif)
        $totalHari = (int) (date_diff(
            date_create($project->tanggal_mulai),
            date_create($project->tanggal_selesai)
        )->days + 1);

        // hitung default durasi dari persen
        $phases = $project->phases->map(function($p) use ($totalHari){
            $dur = (int) floor($totalHari * ($p->persen / 100));
            if ($dur < 1) $dur = 1; // minimal 1 hari
            return [
                'id' => $p->id,
                'urutan' => $p->urutan,
                'nama_tahapan' => $p->nama_tahapan,
                'persen' => $p->persen,
                'durasi_default' => $dur,
            ];
        })->values()->toArray();

        // koreksi pembulatan biar total durasi == totalHari
        $sum = array_sum(array_column($phases, 'durasi_default'));
        $diff = $totalHari - $sum;

        // kalau kurang, tambahkan ke tahap terakhir; kalau lebih, kurangi dari tahap terakhir selama >1
        if ($diff > 0) {
            $phases[count($phases)-1]['durasi_default'] += $diff;
        } elseif ($diff < 0) {
            $need = abs($diff);
            for ($i = count($phases)-1; $i >= 0 && $need > 0; $i--) {
                while ($phases[$i]['durasi_default'] > 1 && $need > 0) {
                    $phases[$i]['durasi_default']--;
                    $need--;
                }
            }
        }

        return view('project.jadwal_generate', compact('project','totalHari','phases'));
    }

    public function generate(Request $request, Project $project)
    {
        $project->load(['phases' => function($q){
            $q->orderBy('urutan');
        }]);

        // validasi kalau tanggal proyek belum bener
        if (empty($project->tanggal_mulai) || empty($project->tanggal_selesai)) {
            return back()->withErrors(['durasi' => 'Tanggal mulai/selesai proyek belum diisi.'])->withInput();
        }

        // total hari proyek (inklusif)
        $totalHari = (int) (date_diff(
            date_create($project->tanggal_mulai),
            date_create($project->tanggal_selesai)
        )->days + 1);

        if ($totalHari <= 0) {
            return back()->withErrors(['durasi' => 'Rentang tanggal proyek tidak valid.'])->withInput();
        }

        $data = $request->validate([
            'mode' => 'required|in:replace,skip',
            'durasi' => 'required|array|min:1',
            'durasi.*' => 'required|integer|min:1',
        ], [
            'mode.required' => 'Mode generate wajib dipilih.',
            'mode.in'       => 'Mode generate tidak valid.',

            'durasi.required' => 'Durasi per tahapan wajib diisi.',
            'durasi.array'    => 'Format durasi tidak valid.',
            'durasi.min'      => 'Minimal harus ada 1 tahapan yang diisi.',

            'durasi.*.required' => 'Durasi tiap tahapan wajib diisi.',
            'durasi.*.integer'  => 'Durasi harus berupa angka bulat.',
            'durasi.*.min'      => 'Durasi minimal 1 hari.',
        ]);

        // ✅ pastikan semua phase proyek ada di request (anti manipulasi form)
        foreach ($project->phases as $ph) {
            if (!array_key_exists($ph->id, $data['durasi'])) {
                return back()->withInput()->withErrors([
                    'durasi' => 'Durasi ada yang belum diisi (semua tahapan wajib diisi).'
                ]);
            }
        }

        // ✅ pastikan tidak ada phase asing yang dikirim user
        $validPhaseIds = $project->phases->pluck('id')->toArray();
        foreach (array_keys($data['durasi']) as $phaseId) {
            if (!in_array((int)$phaseId, $validPhaseIds)) {
                return back()->withInput()->withErrors([
                    'durasi' => 'Ada tahapan tidak valid (terdeteksi manipulasi data).'
                ]);
            }
        }

        // ✅ validasi total durasi tidak boleh > total hari proyek
        $totalInput = array_sum(array_map('intval', $data['durasi']));
        if ($totalInput > $totalHari) {
            return back()->withInput()->withErrors([
                'durasi' => "Total durasi tahapan ({$totalInput} hari) melebihi durasi proyek ({$totalHari} hari)."
            ]);
        }

        // ✅ (opsional) jangan terlalu kecil, biar jadwal tidak bolong.
        // kalau kamu MAU wajib pas = totalHari, aktifkan ini:
        // if ($totalInput !== $totalHari) {
        //     return back()->withInput()->withErrors([
        //         'durasi' => "Total durasi tahapan harus tepat {$totalHari} hari. Saat ini: {$totalInput} hari."
        //     ]);
        // }

        try {
            DB::transaction(function() use ($project, $data) {

                if ($data['mode'] === 'replace') {
                    ProjectPhaseSchedule::where('project_id', $project->id)->delete();
                }

                $current = $project->tanggal_mulai;

                foreach ($project->phases as $ph) {
                    $dur = (int) $data['durasi'][$ph->id];

                    // mode skip: kalau sudah ada jadwal, lewati tapi current maju sesuai jadwal existing
                    $existing = ProjectPhaseSchedule::where('project_id', $project->id)
                        ->where('project_phase_id', $ph->id)
                        ->first();

                    if ($data['mode'] === 'skip' && $existing) {
                        $current = date('Y-m-d', strtotime($existing->tanggal_selesai . ' +1 day'));
                        continue;
                    }

                    $mulai = $current;
                    $selesai = date('Y-m-d', strtotime($mulai . ' + ' . ($dur - 1) . ' days'));

                    // clamp: jangan lewat tanggal selesai proyek
                    if ($selesai > $project->tanggal_selesai) {
                        // kalau sampai sini berarti totalInput <= totalHari tapi jadwal bisa “melewati”
                        // biasanya karena mode skip dan existing jadwal memakan hari terlalu banyak
                        throw new \Exception("Jadwal melewati tanggal selesai proyek karena ada jadwal existing (mode SKIP). Coba pakai mode REPLACE.");
                    }

                    ProjectPhaseSchedule::updateOrCreate(
                        [
                            'project_id' => $project->id,
                            'project_phase_id' => $ph->id,
                        ],
                        [
                            'tanggal_mulai' => $mulai,
                            'tanggal_selesai' => $selesai,
                            'durasi_hari' => $dur,
                        ]
                    );

                    $current = date('Y-m-d', strtotime($selesai . ' +1 day'));
                }
            });
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors([
                'durasi' => $e->getMessage()
            ]);
        }

        return redirect()->route('jadwal.index')->with('success', 'Jadwal otomatis berhasil dibuat.');
    }

}
