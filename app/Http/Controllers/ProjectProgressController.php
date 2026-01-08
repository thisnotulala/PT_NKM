<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\ProjectPhaseProgressLog;
use App\Models\ProjectPhaseProgressPhoto;
use App\Models\Sdm; // ✅ tambahan

// ✅ tambahan: untuk material
use App\Models\ProjectMaterial;
use App\Models\ProjectMaterialUsage;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectProgressController extends Controller
{
    /**
     * LIHAT PROGRESS PROYEK
     * - site manager
     * - administrasi
     * - kepala lapangan
     */
    public function index(Project $project)
    {
        if (!in_array(auth()->user()->role, ['site manager','administrasi','kepala lapangan'])) {
            abort(403);
        }

        $project->load([
            'client',
            'phases' => function($q){
                $q->orderBy('urutan');
            }
        ]);

        $progressTotal = $project->phases->sum(function ($p) {
            return ($p->persen * $p->progress) / 100;
        });

        // ✅ load sdms + material usages untuk ditampilkan di log
        $logs = ProjectPhaseProgressLog::with([
                'phase',
                'photos',
                'sdms',
                // ✅ tambahan: pemakaian material per log
                'materialUsages.projectMaterial'
            ])
            ->where('project_id', $project->id)
            ->latest('tanggal_update')
            ->latest()
            ->get();

        return view('project.progress.index', compact('project','progressTotal','logs'));
    }

    /**
     * FORM UPDATE PROGRESS
     * - hanya kepala lapangan
     */
    public function create(Project $project, ProjectPhase $phase)
    {
        if (auth()->user()->role !== 'kepala lapangan') {
            abort(403);
        }

        if ($phase->project_id !== $project->id) {
            abort(404);
        }

        if ((int) $phase->progress >= 100) {
            return redirect()
                ->route('project.progress.index', $project->id)
                ->with('error', 'Tahapan ini sudah 100%, tidak bisa diupdate.');
        }

        $sdms = Sdm::orderBy('nama')->get();

        // ✅ tambahan: ambil material estimasi untuk proyek ini
        // (disesuaikan kalau nama tabel/kolom/relasi berbeda)
        $projectMaterials = ProjectMaterial::where('project_id', $project->id)
            ->orderBy('id')
            ->get();

        return view('project.progress.create', compact('project','phase','sdms','projectMaterials'));
    }

    /**
     * SIMPAN UPDATE
     * - hanya kepala lapangan
     */
    public function store(Request $request, Project $project, ProjectPhase $phase)
    {
        if (auth()->user()->role !== 'kepala lapangan') {
            abort(403);
        }

        if ($phase->project_id !== $project->id) {
            abort(404);
        }

        if ((int) $phase->progress >= 100) {
            return redirect()
                ->route('project.progress.index', $project->id)
                ->with('error', 'Tahapan ini sudah 100%, tidak bisa diupdate.');
        }

        $data = $request->validate([
            'tanggal_update' => 'required|date',
            'progress'       => 'required|integer|min:0|max:100',
            'catatan'        => 'nullable|string',

            // ✅ SDM yang bekerja (boleh kosong)
            'sdm_ids'        => 'nullable|array',
            'sdm_ids.*'      => 'exists:sdms,id',

            // ✅ tambahan: pemakaian material (boleh kosong)
            // format: materials[PROJECT_MATERIAL_ID] = qty_pakai
            'materials'      => 'nullable|array',
            'materials.*'    => 'nullable|numeric|min:0',

            'foto'           => 'nullable|array|max:5',
            'foto.*'         => 'image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ((int) $data['progress'] < (int) $phase->progress) {
            return back()->withErrors([
                'progress' => "Progress tidak boleh turun dari {$phase->progress}%"
            ]);
        }

        DB::transaction(function () use ($data, $project, $phase, $request) {

            $log = ProjectPhaseProgressLog::create([
                'project_id'       => $project->id,
                'project_phase_id' => $phase->id,
                'tanggal_update'   => $data['tanggal_update'],
                'progress'         => (int)$data['progress'],
                'catatan'          => $data['catatan'] ?? null,
                'created_by'       => auth()->id(),
            ]);

            // ✅ simpan relasi SDM ke log (pivot)
            $sdmIds = $data['sdm_ids'] ?? [];
            $log->sdms()->sync($sdmIds);

            // ✅ tambahan: simpan pemakaian material per log
            // materials key = project_material_id, value = qty_pakai
            $materials = $data['materials'] ?? [];
            foreach ($materials as $projectMaterialId => $qtyPakai) {
                $qty = (float) ($qtyPakai ?? 0);

                // skip kalau kosong / 0
                if ($qty <= 0) continue;

                // pastikan material itu milik project ini (biar aman)
                $pm = ProjectMaterial::where('id', $projectMaterialId)
                    ->where('project_id', $project->id)
                    ->first();

                if (!$pm) continue;

                ProjectMaterialUsage::create([
                    'progress_log_id'   => $log->id,
                    'project_material_id' => $pm->id,
                    'qty_pakai'         => $qty,
                ]);
            }

            // foto
            if ($request->hasFile('foto')) {
                foreach ($request->file('foto') as $file) {
                    $path = $file->store(
                        "progress/project_{$project->id}/phase_{$phase->id}",
                        'public'
                    );

                    ProjectPhaseProgressPhoto::create([
                        'log_id'     => $log->id,
                        'photo_path' => $path,
                    ]);
                }
            }

            // update phase
            $phase->update([
                'progress'         => (int)$data['progress'],
                'last_progress_at' => $data['tanggal_update'],
            ]);
        });

        return redirect()
            ->route('project.progress.index', $project->id)
            ->with('success', 'Progress berhasil disimpan.');
    }

    /**
     * PILIH PROYEK
     * - site manager
     * - administrasi
     * - kepala lapangan
     */
    public function pickProject()
    {
        if (!in_array(auth()->user()->role, ['site manager','administrasi','kepala lapangan'])) {
            abort(403);
        }

        $projects = Project::with(['client','phases'])->get();

        return view('project.progress.pick', compact('projects'));
    }
}
