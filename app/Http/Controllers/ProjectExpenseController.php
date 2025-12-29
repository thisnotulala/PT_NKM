<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\Sdm;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProjectExpenseController extends Controller
{
    public function index(Project $project)
    {
        $project->load('client');

        $expenses = ProjectExpense::with(['sdm','equipment'])
            ->where('project_id', $project->id)
            ->latest('tanggal')
            ->latest()
            ->get();

        $total = $expenses->sum('nominal');

        return view('project.expenses.index', compact('project','expenses','total'));
    }

    public function create(Project $project)
    {
        $sdms = Sdm::orderBy('nama')->get();
        $equipment = Equipment::orderBy('nama_alat')->get();

        return view('project.expenses.create', compact('project','sdms','equipment'));
    }

    public function store(Request $request, Project $project)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'kategori' => 'required|string|max:50',
            'nominal' => 'required|numeric|min:0.01',
            'keterangan' => 'nullable|string|max:255',
            'sdm_id' => 'nullable|exists:sdms,id',
            'equipment_id' => 'nullable|exists:equipment,id',
            'bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        // optional: validasi tanggal harus dalam rentang proyek
        if ($data['tanggal'] < $project->tanggal_mulai || $data['tanggal'] > $project->tanggal_selesai) {
            return back()->withInput()->withErrors([
                'tanggal' => 'Tanggal pengeluaran harus berada dalam rentang tanggal proyek.'
            ]);
        }

        DB::transaction(function() use ($data, $project, $request) {
            $path = null;
            if ($request->hasFile('bukti')) {
                $path = $request->file('bukti')->store("bukti_pengeluaran/project_{$project->id}", 'public');
            }

            ProjectExpense::create([
                'project_id' => $project->id,
                'tanggal' => $data['tanggal'],
                'kategori' => $data['kategori'],
                'nominal' => $data['nominal'],
                'keterangan' => $data['keterangan'] ?? null,
                'sdm_id' => $data['sdm_id'] ?? null,
                'equipment_id' => $data['equipment_id'] ?? null,
                'bukti_path' => $path,
                'created_by' => auth()->id(),
            ]);
        });

        return redirect()->route('project.expenses.index', $project->id)
            ->with('success','Pengeluaran berhasil ditambahkan.');
    }

    public function edit(Project $project, ProjectExpense $expense)
    {
        if ($expense->project_id != $project->id) abort(404);

        $sdms = Sdm::orderBy('nama')->get();
        $equipment = Equipment::orderBy('nama_alat')->get();

        return view('project.expenses.edit', compact('project','expense','sdms','equipment'));
    }

    public function update(Request $request, Project $project, ProjectExpense $expense)
    {
        if ($expense->project_id != $project->id) abort(404);

        $data = $request->validate([
            'tanggal' => 'required|date',
            'kategori' => 'required|string|max:50',
            'nominal' => 'required|numeric|min:0.01',
            'keterangan' => 'nullable|string|max:255',
            'sdm_id' => 'nullable|exists:sdms,id',
            'equipment_id' => 'nullable|exists:equipment,id',
            'bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        if ($data['tanggal'] < $project->tanggal_mulai || $data['tanggal'] > $project->tanggal_selesai) {
            return back()->withInput()->withErrors([
                'tanggal' => 'Tanggal pengeluaran harus berada dalam rentang tanggal proyek.'
            ]);
        }

        DB::transaction(function() use ($data, $project, $expense, $request) {
            if ($request->hasFile('bukti')) {
                if ($expense->bukti_path && Storage::disk('public')->exists($expense->bukti_path)) {
                    Storage::disk('public')->delete($expense->bukti_path);
                }
                $expense->bukti_path = $request->file('bukti')->store("bukti_pengeluaran/project_{$project->id}", 'public');
            }

            $expense->update([
                'tanggal' => $data['tanggal'],
                'kategori' => $data['kategori'],
                'nominal' => $data['nominal'],
                'keterangan' => $data['keterangan'] ?? null,
                'sdm_id' => $data['sdm_id'] ?? null,
                'equipment_id' => $data['equipment_id'] ?? null,
                'bukti_path' => $expense->bukti_path,
            ]);
        });

        return redirect()->route('project.expenses.index', $project->id)
            ->with('success','Pengeluaran berhasil diupdate.');
    }

    public function destroy(Project $project, ProjectExpense $expense)
    {
        if ($expense->project_id != $project->id) abort(404);

        if ($expense->bukti_path && Storage::disk('public')->exists($expense->bukti_path)) {
            Storage::disk('public')->delete($expense->bukti_path);
        }

        $expense->delete();

        return back()->with('success','Pengeluaran berhasil dihapus.');
    }
    public function pickProject()
    {
        $projects = \App\Models\Project::with('client')
            ->orderByDesc('created_at')
            ->get();

        return view('project.expenses.pick', compact('projects'));
    }

}
