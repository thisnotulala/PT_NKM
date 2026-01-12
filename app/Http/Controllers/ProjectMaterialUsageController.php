<?php

namespace App\Http\Controllers;

use App\Models\ProjectMaterial;
use App\Models\ProjectMaterialOut;
use App\Models\ProjectMaterialUsage;
use App\Models\ProjectPhaseProgressLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectMaterialUsageController extends Controller
{
    /**
     * Update material progress (kepala lapangan)
     * dan otomatis catat stok keluar ke project_material_outs
     *
     * Expected input:
     * materials[0][project_material_id]
     * materials[0][qty_pakai]
     */
    public function update(Request $request, ProjectPhaseProgressLog $log)
    {
        if (auth()->user()->role !== 'kepala lapangan') {
            abort(403);
        }

        $data = $request->validate([
            'materials' => 'nullable|array',
            'materials.*.project_material_id' => 'required|exists:project_materials,id',
            'materials.*.qty_pakai' => 'required|numeric|min:0',
        ]);

        // kalau form kosong, anggap tidak ada material
        $items = $data['materials'] ?? [];

        try {
            DB::transaction(function () use ($log, $items) {

                // ✅ asumsi log punya project_id
                $projectId = $log->project_id;

                // 1) hapus dulu semua usage lama untuk log ini (biar sinkron)
                ProjectMaterialUsage::where('progress_log_id', $log->id)->delete();

                // 2) hapus dulu semua OUT lama untuk log ini (biar anti dobel)
                ProjectMaterialOut::where('progress_log_id', $log->id)->delete();

                foreach ($items as $it) {
                    $pmId = (int) ($it['project_material_id'] ?? 0);
                    $qty  = (float) ($it['qty_pakai'] ?? 0);

                    if ($pmId <= 0 || $qty <= 0) continue;

                    // pastikan material milik project yang sama
                    $pm = ProjectMaterial::where('id', $pmId)
                        ->where('project_id', $projectId)
                        ->firstOrFail();

                    // ✅ simpan usage (kalau kamu masih butuh untuk detail progress)
                    ProjectMaterialUsage::create([
                        'progress_log_id' => $log->id,
                        'project_material_id' => $pm->id,
                        'qty_pakai' => $qty,
                    ]);

                    // ✅ simpan stok keluar (ini yang muncul di laporan material)
                    ProjectMaterialOut::create([
                        'project_id'          => $projectId,
                        'project_material_id' => $pm->id,
                        'progress_log_id'     => $log->id,
                        'tanggal'             => $log->tanggal ?? now()->toDateString(), // ganti kalau field tanggal beda
                        'qty_keluar'          => $qty,
                        'catatan'             => 'Auto dari progress log #' . $log->id,
                        'created_by'          => auth()->id(),
                    ]);
                }
            });

            return back()->with('success', 'Material progress tersimpan & stok keluar otomatis tercatat.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
