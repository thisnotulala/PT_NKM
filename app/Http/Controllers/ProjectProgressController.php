<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\ProjectPhaseProgressLog;
use App\Models\ProjectPhaseProgressPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectProgressController extends Controller
{
    public function index(Project $project)
    {
        $project->load(['client','phases' => function($q){
            $q->orderBy('urutan');
        }]);

        // hitung progress proyek dari bobot tahapan
        $progressTotal = $project->phases->sum(function($p){
            return ($p->persen * $p->progress) / 100;
        });

        // ambil logs terbaru
        $logs = ProjectPhaseProgressLog::with(['phase','photos'])
            ->where('project_id', $project->id)
            ->latest('tanggal_update')
            ->latest()
            ->get();

        return view('project.progress.index', compact('project','progressTotal','logs'));
    }

    public function create(Project $project, ProjectPhase $phase)
    {
        if ($phase->project_id != $project->id) abort(404);

        // ✅ jika sudah 100%, tidak boleh input lagi
        if ((int)$phase->progress >= 100) {
            return redirect()->route('project.progress.index', $project->id)
                ->with('error', 'Tahapan ini sudah 100% (selesai), tidak bisa diupdate lagi.');
        }

        return view('project.progress.create', compact('project','phase'));
    }

    public function store(Request $request, Project $project, ProjectPhase $phase)
    {
        if ($phase->project_id != $project->id) abort(404);

        // ✅ jika sudah 100%, tidak boleh input lagi
        if ((int)$phase->progress >= 100) {
            return redirect()->route('project.progress.index', $project->id)
                ->with('error', 'Tahapan ini sudah 100% (selesai), tidak bisa diupdate lagi.');
        }

        $data = $request->validate([
            'tanggal_update' => 'required|date',
            'progress' => 'required|integer|min:0|max:100',
            'catatan' => 'nullable|string',
            'foto' => 'nullable|array|max:5',
            'foto.*' => 'image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // optional: jangan boleh turun
        if ((int)$data['progress'] < (int)$phase->progress) {
            return back()->withInput()->withErrors([
                'progress' => "Progress tidak boleh lebih kecil dari progress sebelumnya ({$phase->progress}%)."
            ]);
        }

        DB::transaction(function() use ($data, $project, $phase, $request) {

            $log = ProjectPhaseProgressLog::create([
                'project_id'       => $project->id,
                'project_phase_id' => $phase->id,
                'tanggal_update'   => $data['tanggal_update'],
                'progress'         => (int)$data['progress'],
                'catatan'          => $data['catatan'] ?? null,
                'created_by'       => auth()->id(),
            ]);

            // simpan foto
            if ($request->hasFile('foto')) {
                foreach ($request->file('foto') as $file) {
                    $path = $file->store("progress/project_{$project->id}/phase_{$phase->id}", 'public');

                    ProjectPhaseProgressPhoto::create([
                        'log_id'     => $log->id,
                        'photo_path' => $path,
                    ]);
                }
            }

            // update nilai progress terbaru di phase
            $phase->update([
                'progress' => (int)$data['progress'],
                'last_progress_at' => $data['tanggal_update'],
            ]);
        });

        return redirect()->route('project.progress.index', $project->id)
            ->with('success', 'Progress tahap berhasil disimpan.');
    }

    public function pickProject()
    {
        $projects = \App\Models\Project::with('client')
            ->orderByDesc('created_at')
            ->get();

        return view('project.progress.pick', compact('projects'));
    }
}
