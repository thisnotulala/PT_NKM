<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectSdm;
use Illuminate\Http\Request;

class ProjectSdmController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $data = $request->validate([
            'sdm_id' => 'required|exists:sdms,id',
            'peran_di_proyek' => 'nullable|string|max:255',
        ]);

        // 1) ✅ Cegah dobel (sdm yang sama tidak boleh ditambahkan 2x di proyek yang sama)
        $exists = ProjectSdm::where('project_id', $project->id)
            ->where('sdm_id', $data['sdm_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'SDM ini sudah ditugaskan di proyek ini.');
        }

        // 2) ✅ Cegah bentrok tanggal dengan proyek lain
        // Bentrok jika:
        // tanggal_mulai_proyek_lain <= tanggal_selesai_proyek_ini
        // DAN tanggal_selesai_proyek_lain >= tanggal_mulai_proyek_ini
        $startNew = $project->tanggal_mulai;
        $endNew   = $project->tanggal_selesai;

        $conflict = ProjectSdm::where('sdm_id', $data['sdm_id'])
            ->where('project_id', '!=', $project->id)
            ->whereHas('project', function ($q) use ($startNew, $endNew) {
                $q->whereDate('tanggal_mulai', '<=', $endNew)
                  ->whereDate('tanggal_selesai', '>=', $startNew);
            })
            ->exists();

        if ($conflict) {
            return back()->with('error', 'SDM ini sedang digunakan di proyek lain pada rentang tanggal proyek ini.');
        }

        // 3) ✅ Jika aman, simpan penugasan
        ProjectSdm::create([
            'project_id' => $project->id,
            'sdm_id' => $data['sdm_id'],
            'peran_di_proyek' => $data['peran_di_proyek'] ?? null,
        ]);

        return back()->with('success', 'SDM berhasil ditambahkan ke proyek.');
    }

    public function destroy(Project $project, ProjectSdm $assignment)
    {
        if ($assignment->project_id != $project->id) abort(404);

        $assignment->delete();

        return back()->with('success', 'SDM berhasil dihapus dari proyek.');
    }
}
