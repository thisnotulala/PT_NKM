<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\ProjectPhaseProgressLog;
use App\Models\ProjectPhaseProgressPhoto;
use App\Models\Sdm; // ✅ tambahan
use Illuminate\Validation\ValidationException;
use App\Models\ProjectMaterialStock;


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
            'materials'                          => 'nullable|array',
            'materials.*.project_material_id'    => 'nullable|integer|exists:project_materials,id',
            'materials.*.qty_pakai'              => 'nullable|numeric|min:0',

            'foto'   => 'nullable|array',
            'foto.*' => 'image|mimes:jpg,jpeg,png|max:2048',

        ]);

        if ((int) $data['progress'] < (int) $phase->progress) {
            return back()->withErrors([
                'progress' => "Progress tidak boleh turun dari {$phase->progress}%"
            ]);
        }
        // ==============================
        // VALIDASI STOK (REAL): masuk - pakai
        // ==============================
        $rows = $data['materials'] ?? [];

        // agregasi total pemakaian per material
        $needByPm = [];
        foreach ($rows as $row) {
            $pmId = $row['project_material_id'] ?? null;
            $qty  = (float) ($row['qty_pakai'] ?? 0);

            if (!$pmId || $qty <= 0) continue;

            $needByPm[$pmId] = ($needByPm[$pmId] ?? 0) + $qty;
        }

        if (!empty($needByPm)) {
            $pmIds = array_keys($needByPm);

            // pastikan material milik project
            $pms = ProjectMaterial::where('project_id', $project->id)
                ->whereIn('id', $pmIds)
                ->get()
                ->keyBy('id');

            // total masuk per material (dari project_material_stocks)
            $masukByPm = ProjectMaterialStock::where('project_id', $project->id)
                ->whereIn('project_material_id', $pmIds)
                ->selectRaw('project_material_id, SUM(qty_masuk) as total_masuk')
                ->groupBy('project_material_id')
                ->pluck('total_masuk', 'project_material_id')
                ->toArray();

            // total pakai per material (dari project_material_usages)
            $pakaiByPm = ProjectMaterialUsage::whereIn('project_material_id', $pmIds)
                ->selectRaw('project_material_id, SUM(qty_pakai) as total_pakai')
                ->groupBy('project_material_id')
                ->pluck('total_pakai', 'project_material_id')
                ->toArray();

            foreach ($needByPm as $pmId => $needQty) {
                if (!$pms->has($pmId)) {
                    throw ValidationException::withMessages([
                        'materials' => 'Material tidak valid untuk proyek ini.'
                    ]);
                }

                $totalMasuk = (float) ($masukByPm[$pmId] ?? 0);
                $totalPakai = (float) ($pakaiByPm[$pmId] ?? 0);
                $tersedia   = $totalMasuk - $totalPakai;

                if ($needQty > $tersedia + 0.00001) {
                    $nama   = $pms[$pmId]->nama_material ?? "Material {$pmId}";
                    $satuan = $pms[$pmId]->satuan ?? '';

                    throw ValidationException::withMessages([
                        'materials' => "Qty pakai '{$nama}' ({$needQty} {$satuan}) melebihi sisa stok ({$tersedia} {$satuan})."
                    ]);
                }
            }
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
            $materialsRows = $data['materials'] ?? [];

            $aggregated = [];

            foreach ($materialsRows as $row) {
                $pmId = $row['project_material_id'] ?? null;
                $qty  = (float) ($row['qty_pakai'] ?? 0);

                // skip kalau row belum lengkap atau qty 0
                if (!$pmId || $qty <= 0) continue;

                // pastikan material milik project ini
                $pm = ProjectMaterial::where('id', $pmId)
                    ->where('project_id', $project->id)
                    ->first();

                if (!$pm) continue;

                ProjectMaterialUsage::create([
                    'progress_log_id'     => $log->id,
                    'project_material_id' => $pm->id,
                    'qty_pakai'           => $qty,
                ]);
            }

            // foto
            if ($request->hasFile('foto')) {
                foreach ($request->file('foto') as $file) {
                    if (!$file) continue;

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

        
    // ambil proyek + client + phases (buat hitung progress total)
    $projects = Project::with([
            'client:id,nama',
            'phases:id,project_id,persen,progress'
        ])
        ->select('id','nama_proyek','client_id','tanggal_mulai','tanggal_selesai')
        ->orderBy('nama_proyek')
        ->get()
        ->map(function ($p) {

            // ✅ hitung progress total dari phases: sum(persen * progress / 100)
            $progressTotal = 0;
            if ($p->phases && $p->phases->count()) {
                $progressTotal = $p->phases->sum(function ($ph) {
                    $bobot = (float) ($ph->persen ?? 0);
                    $prog  = (float) ($ph->progress ?? 0);
                    return ($bobot * $prog) / 100;
                });
            }

            // clamp 0..100
            if ($progressTotal < 0) $progressTotal = 0;
            if ($progressTotal > 100) $progressTotal = 100;

            // tanggal display
            $p->tanggal = ($p->tanggal_mulai ?? '-') . ' s/d ' . ($p->tanggal_selesai ?? '-');

            // ✅ assign progress biar view kamu kebaca
            $p->progress = round($progressTotal, 1);

            // status (opsional)
            $p->status = 'Aktif';
            if ($p->progress >= 100) {
                $p->status = 'Selesai';
            } else {
                // kalau ada tanggal_selesai dan sudah lewat
                if (!empty($p->tanggal_selesai) && now()->toDateString() > $p->tanggal_selesai) {
                    $p->status = 'Terlambat';
                }
            }

            return $p;
        });

    return view('project.progress.pick', compact('projects'));

    }
}
