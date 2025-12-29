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

        $totalHari = (int) (date_diff(
            date_create($project->tanggal_mulai),
            date_create($project->tanggal_selesai)
        )->days + 1);

        $data = $request->validate([
            'mode' => 'required|in:replace,skip',
            'durasi' => 'required|array|min:1',
            'durasi.*' => 'required|integer|min:1',
        ]);

        // pastikan semua phase ada di request
        foreach ($project->phases as $ph) {
            if (!isset($data['durasi'][$ph->id])) {
                return back()->withInput()->withErrors(['durasi' => 'Durasi ada yang belum diisi.']);
            }
        }

        $totalInput = array_sum($data['durasi']);
        if ($totalInput > $totalHari) {
            return back()->withInput()->withErrors([
                'durasi' => "Total durasi tahapan ({$totalInput} hari) melebihi durasi proyek ({$totalHari} hari)."
            ]);
        }

        DB::transaction(function() use ($project, $data) {
            if ($data['mode'] === 'replace') {
                ProjectPhaseSchedule::where('project_id', $project->id)->delete();
            }

            $current = $project->tanggal_mulai;

            foreach ($project->phases as $ph) {
                $dur = (int) $data['durasi'][$ph->id];

                // kalau mode skip dan jadwal sudah ada -> lewati, tapi current tetap maju sesuai jadwal existing
                $existing = ProjectPhaseSchedule::where('project_id', $project->id)
                    ->where('project_phase_id', $ph->id)
                    ->first();

                if ($data['mode'] === 'skip' && $existing) {
                    // majukan current berdasarkan tanggal_selesai existing + 1
                    $current = date('Y-m-d', strtotime($existing->tanggal_selesai . ' +1 day'));
                    continue;
                }

                $mulai = $current;
                $selesai = date('Y-m-d', strtotime($mulai . ' + ' . ($dur - 1) . ' days'));

                // clamp: jangan lewat tanggal selesai proyek
                if ($selesai > $project->tanggal_selesai) {
                    $selesai = $project->tanggal_selesai;
                    // update durasi sesuai clamp
                    $dur = (int)(date_diff(date_create($mulai), date_create($selesai))->days + 1);
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

        return redirect()->route('jadwal.index')->with('success', 'Jadwal otomatis berhasil dibuat.');
    }
}
