<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectSdm;
use Illuminate\Http\Request;

class ProjectSdmController extends Controller
{
    /**
     * Tambah penugasan SDM ke proyek
     * site manager & administrasi
     */
    public function store(Request $request, Project $project)
    {
        // 🔒 Role check
        if (!in_array(auth()->user()->role, ['site manager', 'administrasi'])) {
            abort(403, 'Anda tidak memiliki akses untuk menambahkan SDM ke proyek.');
        }

        $data = $request->validate([
            'sdm_id' => 'required|exists:sdms,id',
            'peran_di_proyek' => 'nullable|string|max:255',
        ]);

        // 1️⃣ Cegah SDM dobel dalam proyek yang sama
        $exists = ProjectSdm::where('project_id', $project->id)
            ->where('sdm_id', $data['sdm_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'SDM ini sudah ditugaskan di proyek ini.');
        }

        // 2️⃣ Cegah bentrok jadwal proyek lain
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

        // 3️⃣ Simpan penugasan
        ProjectSdm::create([
            'project_id' => $project->id,
            'sdm_id' => $data['sdm_id'],
            'peran_di_proyek' => $data['peran_di_proyek'] ?? null,
        ]);

        return back()->with('success', 'SDM berhasil ditambahkan ke proyek.');
    }

    /**
     * Hapus penugasan SDM dari proyek
     * hanya site manager
     */
    public function destroy(Project $project, ProjectSdm $assignment)
    {
        // 🔒 Role check
        if (auth()->user()->role !== 'site manager') {
            abort(403, 'Anda tidak memiliki akses untuk menghapus SDM dari proyek.');
        }

        // Pastikan assignment milik proyek ini
        if ($assignment->project_id != $project->id) {
            abort(404);
        }

        $assignment->delete();

        return back()->with('success', 'SDM berhasil dihapus dari proyek.');
    }
}
