<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectMaterial;
use App\Models\ProjectMaterialStock;
use App\Models\ProjectMaterialRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProjectMaterialController extends Controller
{
    public function pickProject()
    {
        if (!in_array(auth()->user()->role, ['site manager', 'administrasi', 'kepala lapangan'])) {
            abort(403);
        }

        $projects = Project::with('client') // ✅ penting
            ->select('id', 'nama_proyek', 'client_id') // ✅ client_id wajib ikut kalau pakai select
            ->orderBy('nama_proyek')
            ->get();

        return view('project.materials.pick', compact('projects'));
    }

    /**
     * LIST MATERIAL ESTIMASI PROYEK + RINGKAS STOK/Pakai
     */
    public function index(Project $project)
    {
        if (!in_array(auth()->user()->role, ['site manager', 'administrasi', 'kepala lapangan'])) {
            abort(403);
        }

        $materials = ProjectMaterial::where('project_id', $project->id)
            ->withSum('stocks as qty_masuk_total', 'qty_masuk')
            ->withSum('usages as qty_pakai_total', 'qty_pakai')
            ->orderBy('nama_material')
            ->get();

        $stocks = ProjectMaterialStock::with('projectMaterial')
            ->where('project_id', $project->id)
            ->latest('tanggal')
            ->latest()
            ->get();

        $requests = ProjectMaterialRequest::with('projectMaterial')
            ->where('project_id', $project->id)
            ->latest()
            ->get();

        return view('project.materials.index', compact('project', 'materials', 'stocks', 'requests'));
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
            'nama_material'     => $data['nama_material'],
            'satuan'            => $data['satuan'] ?? null,
            'qty_estimasi'      => $data['qty_estimasi'],
            'toleransi_persen'  => $data['toleransi_persen'] ?? null,
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
    // ✅ STOK MASUK MATERIAL (BEBAS, BOLEH > ESTIMASI)
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

        // pastikan material milik project ini
        $pm = ProjectMaterial::where('id', $data['project_material_id'])
            ->where('project_id', $project->id)
            ->first();

        if (!$pm) {
            return back()->with('error', 'Material tidak valid untuk proyek ini.');
        }

        $qtyInput = (float) $data['qty_masuk'];

        ProjectMaterialStock::create([
            'project_id'          => $project->id,
            'project_material_id' => $pm->id,
            'tanggal'             => $data['tanggal'],
            'qty_masuk'           => $qtyInput,
            'catatan'             => $data['catatan'] ?? null,
            'created_by'          => auth()->id(),
        ]);

        return back()->with('success', 'Stok masuk material berhasil ditambahkan (boleh melebihi estimasi).');
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

    // ==================================================
    // ✅ PENGAJUAN MATERIAL (KEPALA LAPANGAN)
    // ==================================================
    public function storeRequest(Request $request, Project $project)
    {
        if (!in_array(auth()->user()->role, ['kepala lapangan'])) {
            abort(403);
        }

        $data = $request->validate([
            'project_material_id' => 'required|exists:project_materials,id',
            'tanggal_pengajuan'   => 'required|date',
            'qty'                 => 'required|numeric|min:0.01',
            'catatan'             => 'nullable|string|max:255',
        ]);

        $pm = ProjectMaterial::where('id', $data['project_material_id'])
            ->where('project_id', $project->id)
            ->first();

        if (!$pm) {
            return back()->with('error', 'Material tidak valid untuk proyek ini.');
        }

        ProjectMaterialRequest::create([
            'project_id'          => $project->id,
            'project_material_id' => $pm->id,
            'tanggal_pengajuan'   => $data['tanggal_pengajuan'],
            'qty'                 => (float) $data['qty'],
            'catatan'             => $data['catatan'] ?? null,
            'status'              => 'pending',
            'requested_by'        => auth()->id(),
        ]);

        return back()->with('success', 'Pengajuan material berhasil dikirim (menunggu ACC Site Manager).');
    }

    // ==================================================
    // ✅ APPROVE: otomatis jadi stok masuk (BEBAS > ESTIMASI)
    // ==================================================
    public function approveRequest(Request $request, Project $project, ProjectMaterialRequest $materialRequest)
    {
        if (auth()->user()->role !== 'site manager') {
            abort(403);
        }

        if ($materialRequest->project_id !== $project->id) {
            abort(404);
        }

        if ($materialRequest->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        $data = $request->validate([
            'approval_note' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($project, $materialRequest, $data) {

            $pm = ProjectMaterial::where('id', $materialRequest->project_material_id)
                ->where('project_id', $project->id)
                ->first();

            if (!$pm) {
                throw new \Exception("Material tidak valid.");
            }

            $qtyInput = (float) $materialRequest->qty;

            $stock = ProjectMaterialStock::create([
                'project_id'          => $project->id,
                'project_material_id' => $pm->id,
                'tanggal'             => $materialRequest->tanggal_pengajuan,
                'qty_masuk'           => $qtyInput,
                'catatan'             => trim(($materialRequest->catatan ?? '') . ' | ACC: ' . (auth()->user()->name ?? 'Site Manager') . ' | ' . ($data['approval_note'] ?? '')) ?: null,
                'created_by'          => auth()->id(),
            ]);

            $materialRequest->update([
                'status'        => 'approved',
                'approved_by'   => auth()->id(),
                'approved_at'   => now(),
                'approval_note' => $data['approval_note'] ?? null,
                'stock_id'      => $stock->id,
            ]);
        });

        return back()->with('success', 'Pengajuan di-ACC dan otomatis masuk ke Stok Masuk (boleh melebihi estimasi).');
    }

    public function rejectRequest(Request $request, Project $project, ProjectMaterialRequest $materialRequest)
    {
        if (auth()->user()->role !== 'site manager') {
            abort(403);
        }

        if ($materialRequest->project_id !== $project->id) {
            abort(404);
        }

        if ($materialRequest->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        $data = $request->validate([
            'approval_note' => 'required|string|max:255',
        ]);

        $materialRequest->update([
            'status'        => 'rejected',
            'approved_by'   => auth()->id(),
            'approved_at'   => now(),
            'approval_note' => $data['approval_note'],
        ]);

        return back()->with('success', 'Pengajuan ditolak.');
    }
}
