<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectMaterial;
use App\Models\ProjectMaterialStock;
use Illuminate\Http\Request;

class ProjectMaterialController extends Controller
{
    public function pickProject()
    {
        if (!in_array(auth()->user()->role, ['site manager', 'administrasi'])) {
            abort(403);
        }

        $projects = Project::select('id','nama_proyek')
            ->orderBy('nama_proyek')
            ->get();

        return view('project.materials.pick', compact('projects'));
    }


    /**
     * LIST MATERIAL ESTIMASI PROYEK + RINGKAS STOK/Pakai
     */
    public function index(Project $project)
    {
        if (!in_array(auth()->user()->role, ['site manager', 'administrasi'])) {
            abort(403);
        }

        // ✅ material estimasi + total stok masuk + total pakai
        $materials = ProjectMaterial::where('project_id', $project->id)
            ->withSum('stocks as qty_masuk_total', 'qty_masuk')
            ->withSum('usages as qty_pakai_total', 'qty_pakai')
            ->orderBy('nama_material')
            ->get();

        // ✅ riwayat stok masuk terbaru
        $stocks = ProjectMaterialStock::with('projectMaterial')
            ->where('project_id', $project->id)
            ->latest('tanggal')
            ->latest()
            ->get();

        return view('project.materials.index', compact('project', 'materials', 'stocks'));
    }

    /**
     * SIMPAN MATERIAL ESTIMASI
     */
    public function store(Request $request, Project $project)
    {
        if (!in_array(auth()->user()->role, ['site manager', 'administrasi'])) {
            abort(403);
        }

        $data = $request->validate([
            'nama_material'      => 'required|string|max:255',
            'satuan'             => 'nullable|string|max:50',
            'qty_estimasi'       => 'required|numeric|min:0',
            'toleransi_persen'   => 'nullable|integer|min:0|max:100',
        ]);

        ProjectMaterial::create([
            'project_id'        => $project->id,
            'nama_material'     => $data['nama_material'],
            'satuan'            => $data['satuan'] ?? null,
            'qty_estimasi'      => $data['qty_estimasi'],
            'toleransi_persen'  => $data['toleransi_persen'] ?? null,
        ]);

        return back()->with('success', 'Material estimasi berhasil ditambahkan.');
    }

    /**
     * UPDATE ESTIMASI MATERIAL
     */
    public function update(Request $request, Project $project, ProjectMaterial $projectMaterial)
    {
        if (!in_array(auth()->user()->role, ['site manager', 'administrasi'])) {
            abort(403);
        }

        if ($projectMaterial->project_id !== $project->id) {
            abort(404);
        }

        $data = $request->validate([
            'nama_material'      => 'required|string|max:255',
            'satuan'             => 'nullable|string|max:50',
            'qty_estimasi'       => 'required|numeric|min:0',
            'toleransi_persen'   => 'nullable|integer|min:0|max:100',
        ]);

        $projectMaterial->update([
            'nama_material' => $data['nama_material'],
            'satuan' => $data['satuan'] ?? null,
            'qty_estimasi' => $data['qty_estimasi'],
            'toleransi_persen' => $data['toleransi_persen'] ?? null,
        ]);

        return back()->with('success', 'Material estimasi berhasil diperbarui.');
    }

    /**
     * HAPUS MATERIAL ESTIMASI
     */
    public function destroy(Project $project, ProjectMaterial $projectMaterial)
    {
        if (auth()->user()->role !== 'site manager') {
            abort(403);
        }

        if ($projectMaterial->project_id !== $project->id) {
            abort(404);
        }

        $projectMaterial->delete();

        return back()->with('success', 'Material estimasi dihapus.');
    }

    // ==================================================
    // ✅ TAMBAHAN: STOK MASUK MATERIAL
    // ==================================================
    public function storeStock(Request $request, Project $project)
    {
        if (!in_array(auth()->user()->role, ['site manager', 'administrasi'])) {
            abort(403);
        }

        $data = $request->validate([
            'project_material_id' => 'required|exists:project_materials,id',
            'tanggal'             => 'required|date',
            'qty_masuk'           => 'required|numeric|min:0.01',
            'catatan'             => 'nullable|string|max:255',
        ]);

        // ✅ pastikan material milik project ini
        $pm = ProjectMaterial::where('id', $data['project_material_id'])
            ->where('project_id', $project->id)
            ->first();

        if (!$pm) {
            return back()->with('error', 'Material tidak valid untuk proyek ini.');
        }

        // ============================
        // ✅ KONTROL: batas aman stok
        // ============================
        $estimasi = (float) ($pm->qty_estimasi ?? 0);
        $tol      = (float) ($pm->toleransi_persen ?? 0);
        $batas    = $estimasi * (1 + ($tol / 100));

        // total stok masuk saat ini (sebelum input baru)
        $totalMasukSekarang = (float) ProjectMaterialStock::where('project_id', $project->id)
            ->where('project_material_id', $pm->id)
            ->sum('qty_masuk');

        $qtyInput = (float) $data['qty_masuk'];
        $totalSetelahInput = $totalMasukSekarang + $qtyInput;

        // ✅ jika melewati batas, catatan WAJIB
        if ($estimasi > 0 && $totalSetelahInput > $batas) {
            if (empty($data['catatan'])) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'catatan' => 'Catatan/alasan wajib diisi karena stok masuk melebihi estimasi (+ toleransi).'
                    ]);
            }
        }

        ProjectMaterialStock::create([
            'project_id'          => $project->id,
            'project_material_id' => $pm->id,
            'tanggal'             => $data['tanggal'],
            'qty_masuk'           => $qtyInput,
            'catatan'             => $data['catatan'] ?? null,
            'created_by'          => auth()->id(),
        ]);

        // ✅ pesan beda kalau melewati batas
        if ($estimasi > 0 && $totalSetelahInput > $batas) {
            return back()->with('success', 'Stok masuk tersimpan, namun MELEBIHI estimasi (+ toleransi).');
        }

        return back()->with('success', 'Stok masuk material berhasil ditambahkan.');
    }


    public function destroyStock(Project $project, ProjectMaterialStock $stock)
    {
        if (auth()->user()->role !== 'site manager') {
            abort(403);
        }

        if ($stock->project_id !== $project->id) {
            abort(404);
        }

        $stock->delete();

        return back()->with('success', 'Riwayat stok masuk berhasil dihapus.');
    }
}
