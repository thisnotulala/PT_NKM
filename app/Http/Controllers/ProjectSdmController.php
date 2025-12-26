<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Sdm;
use Illuminate\Http\Request;

class ProjectSdmController extends Controller
{
    public function edit(Project $project)
    {
        $sdms = Sdm::orderBy('nama')->get();
        $project->load('sdms');
        return view('project.sdm', compact('project', 'sdms'));
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'sdm_ids' => 'nullable|array',
            'sdm_ids.*' => 'exists:sdms,id',
        ]);

        // sync: overwrite daftar SDM proyek
        $project->sdms()->sync($data['sdm_ids'] ?? []);

        return redirect()->route('project.show', $project->id)
            ->with('success', 'SDM proyek berhasil diperbarui.');
    }
}
