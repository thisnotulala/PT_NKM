<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\ProjectPhaseProgressLog;
use App\Models\ProjectPhaseProgressPhoto;
use App\Models\Sdm;
use Illuminate\Validation\ValidationException;
use App\Models\ProjectMaterialStock;

// ✅ tambahan: untuk material
use App\Models\ProjectMaterial;
use App\Models\ProjectMaterialUsage;

// ✅ tambahan: stok keluar auto (muncul di halaman Project Material)
use App\Models\ProjectMaterialOut;

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

        // ✅ material estimasi untuk proyek ini
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
        if (auth()->user()->role !== 'kepala lapangan') abort(403);
        if ($phase->project_id !== $project->id) abort(404);

        // kalau sudah 100, stop
        if ((int) $phase->progress >= 100) {
            return redirect()
                ->route('project.progress.index', $project->id)
                ->with('error', 'Tahapan ini sudah 100%, tidak bisa diupdate.');
        }

        $data = $request->validate([
            'tanggal_update' => 'required|date',

            // ⬇️ input ini dianggap TAMBAHAN, bukan total
            'progress'       => 'required|integer|min:1|max:100',

            'catatan'        => 'nullable|string',
            'sdm_ids'        => 'nullable|array',
            'sdm_ids.*'      => 'exists:sdms,id',

            'materials'                       => 'nullable|array',
            'materials.*.project_material_id' => 'nullable|required_with:materials.*.qty_pakai|integer|exists:project_materials,id',
            'materials.*.qty_pakai'           => 'nullable|required_with:materials.*.project_material_id|numeric|min:0',

            // ✅ upload > 1 foto
            'foto'           => 'nullable|array',
            'foto.*'         => 'image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // ✅ hitung TOTAL BARU = sebelumnya + input
        $prevProgress  = (int) $phase->progress;
        $deltaProgress = (int) $data['progress'];
        $newProgress   = $prevProgress + $deltaProgress;

        // batasin max 100
        if ($newProgress > 100) $newProgress = 100;

        // =========================================================
        // ✅ VALIDASI STOK: qty_pakai tidak boleh > stok tersedia
        // ✅ stok tersedia dihitung dari: masuk - keluar(outs)
        // =========================================================
        $materialsRows = $data['materials'] ?? [];

        $needByPm = [];
        foreach ($materialsRows as $row) {
            $pmId = $row['project_material_id'] ?? null;
            $qty  = (float) ($row['qty_pakai'] ?? 0);
            if (!$pmId || $qty <= 0) continue;
            $needByPm[$pmId] = ($needByPm[$pmId] ?? 0) + $qty;
        }

        if (!empty($needByPm)) {
            $pms = ProjectMaterial::where('project_id', $project->id)
                ->whereIn('id', array_keys($needByPm))
                ->get()
                ->keyBy('id');

            foreach ($needByPm as $pmId => $needQty) {
                if (!$pms->has($pmId)) {
                    throw ValidationException::withMessages([
                        'materials' => "Ada material yang tidak valid untuk proyek ini."
                    ]);
                }
            }

            // total masuk
            $masukByPm = ProjectMaterialStock::where('project_id', $project->id)
                ->whereIn('project_material_id', array_keys($needByPm))
                ->selectRaw('project_material_id, SUM(qty_masuk) as total_masuk')
                ->groupBy('project_material_id')
                ->pluck('total_masuk', 'project_material_id')
                ->toArray();

            // total keluar (AUTO dari progress + manual, semuanya di outs)
            $keluarByPm = ProjectMaterialOut::where('project_id', $project->id)
                ->whereIn('project_material_id', array_keys($needByPm))
                ->selectRaw('project_material_id, SUM(qty_keluar) as total_keluar')
                ->groupBy('project_material_id')
                ->pluck('total_keluar', 'project_material_id')
                ->toArray();

            foreach ($needByPm as $pmId => $needQty) {
                $totalMasuk  = (float) ($masukByPm[$pmId] ?? 0);
                $totalKeluar = (float) ($keluarByPm[$pmId] ?? 0);
                $tersedia    = $totalMasuk - $totalKeluar;

                if ($needQty > $tersedia + 0.00001) {
                    $nama = $pms[$pmId]->nama_material ?? "Material ID {$pmId}";
                    throw ValidationException::withMessages([
                        'materials' => "Pemakaian '{$nama}' ({$needQty}) melebihi stok tersedia ({$tersedia})."
                    ]);
                }
            }
        }

        DB::transaction(function () use ($data, $project, $phase, $request, $deltaProgress, $newProgress) {

            // ✅ LOG simpan DELTA (+25), bukan total
            $log = ProjectPhaseProgressLog::create([
                'project_id'       => $project->id,
                'project_phase_id' => $phase->id,
                'tanggal_update'   => $data['tanggal_update'],

                // ⬇️ SIMPAN TAMBAHAN
                'progress'         => $deltaProgress,

                'catatan'          => $data['catatan'] ?? null,
                'created_by'       => auth()->id(),
            ]);

            // SDM
            $log->sdms()->sync($data['sdm_ids'] ?? []);

            // ✅ Material usage + stok keluar otomatis
            $materialsRows = $data['materials'] ?? [];
            foreach ($materialsRows as $row) {
                $pmId = $row['project_material_id'] ?? null;
                $qty  = (float) ($row['qty_pakai'] ?? 0);
                if (!$pmId || $qty <= 0) continue;

                $pm = ProjectMaterial::where('id', $pmId)
                    ->where('project_id', $project->id)
                    ->lockForUpdate()
                    ->first();

                if (!$pm) continue;

                // ✅ tetap simpan detail pemakaian per log (buat tampilan log)
                ProjectMaterialUsage::create([
                    'progress_log_id'     => $log->id,
                    'project_material_id' => $pm->id,
                    'qty_pakai'           => $qty,
                ]);

                // ✅ INI yang bikin muncul di halaman Project Material -> Riwayat Stok Keluar (Auto dari Progress)
                ProjectMaterialOut::create([
                    'project_id'          => $project->id,
                    'project_material_id' => $pm->id,
                    'tanggal'             => $data['tanggal_update'],
                    'qty_keluar'          => $qty,
                    'catatan'             => 'Auto dari progress: ' . ($phase->nama_tahapan ?? 'Tahapan') . ' (+' . $deltaProgress . '%)',
                    'created_by'          => auth()->id(),
                    'progress_log_id'     => $log->id,
                ]);
            }

            // Foto (bisa > 1)
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

            // ✅ UPDATE PHASE pakai TOTAL BARU
            $phase->update([
                'progress'         => $newProgress,
                'last_progress_at' => $data['tanggal_update'],
            ]);
        });

        return redirect()
            ->route('project.progress.index', $project->id)
            ->with('success', "Progress berhasil ditambahkan (+{$deltaProgress}%). Total sekarang {$newProgress}%.");
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
