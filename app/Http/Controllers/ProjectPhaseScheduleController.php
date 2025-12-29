<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\ProjectPhaseSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectPhaseScheduleController extends Controller
{
    public function index()
    {
        $schedules = ProjectPhaseSchedule::with(['project','phase'])
            ->latest()->get();

        return view('jadwal.index', compact('schedules'));
    }

    public function create()
    {
        $projects = Project::orderBy('nama_proyek')->get();
        return view('jadwal.create', compact('projects'));
    }

    // untuk dropdown tahapan sesuai proyek (AJAX)
    public function phasesByProject(Project $project)
    {
        $phases = ProjectPhase::where('project_id', $project->id)
            ->orderBy('urutan')
            ->get(['id','nama_tahapan','persen','urutan']);

        return response()->json([
            'project' => [
                'id' => $project->id,
                'tanggal_mulai' => $project->tanggal_mulai,
                'tanggal_selesai' => $project->tanggal_selesai,
            ],
            'phases' => $phases
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'project_phase_id' => 'required|exists:project_phases,id',
            'tanggal_mulai' => 'required|date',
            'durasi_hari' => 'required|integer|min:1',
        ]);

        $project = Project::findOrFail($data['project_id']);
        $phase = ProjectPhase::findOrFail($data['project_phase_id']);

        if ($phase->project_id != $project->id) {
            return back()->withInput()->withErrors(['project_phase_id' => 'Tahapan tidak sesuai proyek.']);
        }

        // hitung tanggal_selesai otomatis dari durasi
        $tanggalSelesai = date('Y-m-d', strtotime($data['tanggal_mulai'] . ' + ' . ($data['durasi_hari'] - 1) . ' days'));

        // validasi batas proyek
        if ($data['tanggal_mulai'] < $project->tanggal_mulai || $tanggalSelesai > $project->tanggal_selesai) {
            return back()->withInput()->withErrors([
                'tanggal_mulai' => "Jadwal harus di dalam rentang proyek ({$project->tanggal_mulai} s/d {$project->tanggal_selesai})."
            ]);
        }

        // validasi 1 tahap 1 jadwal
        $exists = ProjectPhaseSchedule::where('project_id', $project->id)
            ->where('project_phase_id', $phase->id)
            ->exists();
        if ($exists) {
            return back()->withInput()->withErrors(['project_phase_id' => 'Jadwal untuk tahap ini sudah ada.']);
        }

        // validasi overlap antar jadwal dalam proyek (biar tidak tabrakan)
        $overlap = ProjectPhaseSchedule::where('project_id', $project->id)
            ->where(function ($q) use ($data, $tanggalSelesai) {
                $q->whereBetween('tanggal_mulai', [$data['tanggal_mulai'], $tanggalSelesai])
                  ->orWhereBetween('tanggal_selesai', [$data['tanggal_mulai'], $tanggalSelesai])
                  ->orWhere(function ($qq) use ($data, $tanggalSelesai) {
                      $qq->where('tanggal_mulai', '<=', $data['tanggal_mulai'])
                         ->where('tanggal_selesai', '>=', $tanggalSelesai);
                  });
            })->exists();

        if ($overlap) {
            return back()->withInput()->withErrors([
                'tanggal_mulai' => 'Tanggal tahap bentrok dengan jadwal tahap lain pada proyek ini.'
            ]);
        }

        ProjectPhaseSchedule::create([
            'project_id' => $project->id,
            'project_phase_id' => $phase->id,
            'tanggal_mulai' => $data['tanggal_mulai'],
            'tanggal_selesai' => $tanggalSelesai,
            'durasi_hari' => $data['durasi_hari'],
        ]);

        return redirect()->route('jadwal.index')->with('success', 'Jadwal tahap berhasil dibuat.');
    }

    public function edit(ProjectPhaseSchedule $schedule)
    {
        $projects = Project::orderBy('nama_proyek')->get();
        $schedule->load(['project','phase']);
        return view('jadwal.edit', compact('schedule','projects'));
    }

    public function update(Request $request, ProjectPhaseSchedule $schedule)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'project_phase_id' => 'required|exists:project_phases,id',
            'tanggal_mulai' => 'required|date',
            'durasi_hari' => 'required|integer|min:1',
        ]);

        $project = Project::findOrFail($data['project_id']);
        $phase = ProjectPhase::findOrFail($data['project_phase_id']);

        if ($phase->project_id != $project->id) {
            return back()->withInput()->withErrors(['project_phase_id' => 'Tahapan tidak sesuai proyek.']);
        }

        $tanggalSelesai = date('Y-m-d', strtotime($data['tanggal_mulai'] . ' + ' . ($data['durasi_hari'] - 1) . ' days'));

        if ($data['tanggal_mulai'] < $project->tanggal_mulai || $tanggalSelesai > $project->tanggal_selesai) {
            return back()->withInput()->withErrors([
                'tanggal_mulai' => "Jadwal harus di dalam rentang proyek ({$project->tanggal_mulai} s/d {$project->tanggal_selesai})."
            ]);
        }

        $exists = ProjectPhaseSchedule::where('project_id', $project->id)
            ->where('project_phase_id', $phase->id)
            ->where('id', '!=', $schedule->id)
            ->exists();
        if ($exists) {
            return back()->withInput()->withErrors(['project_phase_id' => 'Jadwal untuk tahap ini sudah ada.']);
        }

        $overlap = ProjectPhaseSchedule::where('project_id', $project->id)
            ->where('id', '!=', $schedule->id)
            ->where(function ($q) use ($data, $tanggalSelesai) {
                $q->whereBetween('tanggal_mulai', [$data['tanggal_mulai'], $tanggalSelesai])
                  ->orWhereBetween('tanggal_selesai', [$data['tanggal_mulai'], $tanggalSelesai])
                  ->orWhere(function ($qq) use ($data, $tanggalSelesai) {
                      $qq->where('tanggal_mulai', '<=', $data['tanggal_mulai'])
                         ->where('tanggal_selesai', '>=', $tanggalSelesai);
                  });
            })->exists();

        if ($overlap) {
            return back()->withInput()->withErrors([
                'tanggal_mulai' => 'Tanggal tahap bentrok dengan jadwal tahap lain pada proyek ini.'
            ]);
        }

        $schedule->update([
            'project_id' => $project->id,
            'project_phase_id' => $phase->id,
            'tanggal_mulai' => $data['tanggal_mulai'],
            'tanggal_selesai' => $tanggalSelesai,
            'durasi_hari' => $data['durasi_hari'],
        ]);

        return redirect()->route('jadwal.index')->with('success', 'Jadwal tahap berhasil diupdate.');
    }

    public function destroy(ProjectPhaseSchedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil dihapus.');
    }
}
