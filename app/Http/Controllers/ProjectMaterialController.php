<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectMaterial;
use Illuminate\Http\Request;

class ProjectMaterialController extends Controller
{
    public function index(Project $project)
    {
        if (!in_array(auth()->user()->role, ['site manager', 'administrasi'])) {
            abort(403);
        }

        $materials = ProjectMaterial::where('project_id', $project->id)
            ->orderBy('nama_material')
            ->get();

        return view('project.materials.index', compact('project', 'materials'));
    }

    public function store(Request $request, Project $project)
    {
        if (!in_array(auth()->user()->role, ['site manager', 'administrasi'])) {
            abort(403);
        }

        $data = $request->validate([
            'nama_material' => 'required|string|max:255',
            'satuan' => 'nullable|string|max:50',
            'qty_estimasi' => 'required|numeric|min:0',
            'toleransi_persen' => 'nullable|integer|min:0|max:100',
        ]);

        ProjectMaterial::create([
            'project_id' => $project->id,
            'nama_material' => $data['nama_material'],
            'satuan' => $data['satuan'] ?? null,
            'qty_estimasi' => $data['qty_estimasi'],
            'toleransi_persen' => $data['toleransi_persen'] ?? null,
        ]);

        return back()->with('success', 'Material estimasi berhasil ditambahkan.');
    }

    public function destroy(Project $project, ProjectMaterial $material)
    {
        if (auth()->user()->role !== 'site manager') {
            abort(403);
        }

        if ($material->project_id !== $project->id) abort(404);

        $material->delete();

        return back()->with('success', 'Material estimasi dihapus.');
    }
}
