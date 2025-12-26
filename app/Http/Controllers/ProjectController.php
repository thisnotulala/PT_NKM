<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectPhase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('client')->latest()->get();
        return view('project.index', compact('projects'));
    }

    public function create()
    {
        $clients = Client::orderBy('nama')->get();
        return view('project.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_proyek'     => 'required|string|max:255',
            'client_id'       => 'required|exists:clients,id',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'dokumen'         => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg|max:5120',

            'tahapan'                => 'required|array|min:1',
            'tahapan.*.nama_tahapan' => 'required|string|max:255',
            'tahapan.*.persen'       => 'required|integer|min:0|max:100',
        ]);

        $total = collect($data['tahapan'])->sum(fn($t) => (int)$t['persen']);
        if ($total !== 100) {
            return back()->withInput()->withErrors([
                'tahapan_total' => "Total persentase tahapan harus 100%. Saat ini: {$total}%",
            ]);
        }

        DB::transaction(function () use ($request, $data) {
            $path = null;
            if ($request->hasFile('dokumen')) {
                $path = $request->file('dokumen')->store('dokumen_proyek', 'public');
            }

            $project = Project::create([
                'nama_proyek'     => $data['nama_proyek'],
                'client_id'       => $data['client_id'],
                'tanggal_mulai'   => $data['tanggal_mulai'],
                'tanggal_selesai' => $data['tanggal_selesai'],
                'dokumen'         => $path,
            ]);

            foreach ($data['tahapan'] as $i => $t) {
                ProjectPhase::create([
                    'project_id'   => $project->id,
                    'nama_tahapan' => $t['nama_tahapan'],
                    'persen'       => (int)$t['persen'],
                    'urutan'       => $i + 1,
                ]);
            }
        });

        return redirect()->route('project.index')->with('success', 'Proyek berhasil ditambahkan.');
    }

    public function show(Project $project)
    {
        $project->load(['client', 'phases']);
        return view('project.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $project->load('phases');
        $clients = Client::orderBy('nama')->get();
        return view('project.edit', compact('project', 'clients'));
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'nama_proyek'     => 'required|string|max:255',
            'client_id'       => 'required|exists:clients,id',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'dokumen'         => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg|max:5120',

            'tahapan'                => 'required|array|min:1',
            'tahapan.*.nama_tahapan' => 'required|string|max:255',
            'tahapan.*.persen'       => 'required|integer|min:0|max:100',
        ]);

        $total = collect($data['tahapan'])->sum(fn($t) => (int)$t['persen']);
        if ($total !== 100) {
            return back()->withInput()->withErrors([
                'tahapan_total' => "Total persentase tahapan harus 100%. Saat ini: {$total}%",
            ]);
        }

        DB::transaction(function () use ($request, $data, $project) {
            // update dokumen (kalau upload baru)
            if ($request->hasFile('dokumen')) {
                if ($project->dokumen && Storage::disk('public')->exists($project->dokumen)) {
                    Storage::disk('public')->delete($project->dokumen);
                }
                $project->dokumen = $request->file('dokumen')->store('dokumen_proyek', 'public');
            }

            $project->nama_proyek = $data['nama_proyek'];
            $project->client_id = $data['client_id'];
            $project->tanggal_mulai = $data['tanggal_mulai'];
            $project->tanggal_selesai = $data['tanggal_selesai'];
            $project->save();

            // cara gampang: hapus semua phases lama, insert ulang sesuai input
            ProjectPhase::where('project_id', $project->id)->delete();

            foreach ($data['tahapan'] as $i => $t) {
                ProjectPhase::create([
                    'project_id'   => $project->id,
                    'nama_tahapan' => $t['nama_tahapan'],
                    'persen'       => (int)$t['persen'],
                    'urutan'       => $i + 1,
                ]);
            }
        });

        return redirect()->route('project.index')->with('success', 'Proyek berhasil diupdate.');
    }

    public function destroy(Project $project)
    {
        if ($project->dokumen && Storage::disk('public')->exists($project->dokumen)) {
            Storage::disk('public')->delete($project->dokumen);
        }
        $project->delete();

        return redirect()->route('project.index')->with('success', 'Proyek berhasil dihapus.');
    }
}
