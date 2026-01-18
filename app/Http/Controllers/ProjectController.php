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
    public function __construct()
    {
        $this->middleware('auth');

        // tambah & edit hanya role tertentu
        $this->middleware('role:site manager,administrasi')
            ->only(['create', 'store', 'edit', 'update']);
    }

    /**
     * LIST PROYEK
     */
    public function index()
    {
        $projects = Project::with('client')->latest()->get();
        return view('project.index', compact('projects'));
    }

    /**
     * FORM TAMBAH PROYEK
     */
    public function create()
    {
        $clients = Client::orderBy('nama')->get();
        return view('project.create', compact('clients'));
    }

    /**
     * SIMPAN PROYEK BARU
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_proyek'     => 'required|string|max:255',
            'client_id'       => 'required|exists:clients,id',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',

            'dokumen'         => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg|max:5120',
            'rab'             => 'nullable|file|mimes:pdf,xls,xlsx|max:5120',

            'tahapan'                => 'required|array|min:1',
            'tahapan.*.nama_tahapan' => 'required|string|max:255',
            'tahapan.*.persen'       => 'required|integer|min:0|max:100',
        ], [
            'nama_proyek.required' => 'Nama proyek wajib diisi.',
            'nama_proyek.max'      => 'Nama proyek maksimal 255 karakter.',

            'client_id.required' => 'Client wajib dipilih.',
            'client_id.exists'   => 'Client yang dipilih tidak ditemukan.',

            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_mulai.date'     => 'Format tanggal mulai tidak valid.',

            'tanggal_selesai.required'      => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.date'          => 'Format tanggal selesai tidak valid.',
            'tanggal_selesai.after_or_equal'=> 'Tanggal selesai harus sama atau setelah tanggal mulai.',

            'dokumen.file'  => 'Dokumen harus berupa file.',
            'dokumen.mimes' => 'Dokumen harus berformat: pdf/doc/docx/xls/xlsx/png/jpg/jpeg.',
            'dokumen.max'   => 'Ukuran dokumen maksimal 5MB.',

            'rab.file'  => 'RAB harus berupa file.',
            'rab.mimes' => 'RAB harus berformat: PDF atau Excel (xls/xlsx).',
            'rab.max'   => 'Ukuran RAB maksimal 5MB.',

            'tahapan.required' => 'Minimal harus ada 1 tahapan.',
            'tahapan.array'    => 'Format tahapan tidak valid.',
            'tahapan.min'      => 'Minimal harus ada 1 tahapan.',

            'tahapan.*.nama_tahapan.required' => 'Nama tahapan wajib diisi.',
            'tahapan.*.nama_tahapan.max'      => 'Nama tahapan maksimal 255 karakter.',

            'tahapan.*.persen.required' => 'Persentase tahapan wajib diisi.',
            'tahapan.*.persen.integer'  => 'Persentase tahapan harus berupa angka bulat.',
            'tahapan.*.persen.min'      => 'Persentase tahapan minimal 0.',
            'tahapan.*.persen.max'      => 'Persentase tahapan maksimal 100.',
        ]);

        // validasi total tahapan
        $total = collect($data['tahapan'])->sum(fn ($t) => (int) $t['persen']);
        if ($total !== 100) {
            return back()->withInput()->withErrors([
                'tahapan_total' => "Total persentase tahapan harus 100%. Saat ini: {$total}%",
            ]);
        }

        DB::transaction(function () use ($request, $data) {

            // upload dokumen
            $dokumenPath = null;
            if ($request->hasFile('dokumen')) {
                $dokumenPath = $request->file('dokumen')
                    ->store('dokumen_proyek', 'public');
            }

            // upload RAB
            $rabPath = null;
            if ($request->hasFile('rab')) {
                $rabPath = $request->file('rab')
                    ->store('rab_proyek', 'public');
            }

            // simpan proyek
            $project = Project::create([
                'nama_proyek'     => $data['nama_proyek'],
                'client_id'       => $data['client_id'],
                'tanggal_mulai'   => $data['tanggal_mulai'],
                'tanggal_selesai' => $data['tanggal_selesai'],
                'dokumen'         => $dokumenPath,
                'rab_path'        => $rabPath,
            ]);

            // simpan tahapan
            foreach ($data['tahapan'] as $i => $t) {
                ProjectPhase::create([
                    'project_id'   => $project->id,
                    'nama_tahapan' => $t['nama_tahapan'],
                    'persen'       => (int) $t['persen'],
                    'urutan'       => $i + 1,
                ]);
            }
        });

        return redirect()
            ->route('project.index')
            ->with('success', 'Proyek berhasil ditambahkan.');
    }

    /**
     * DETAIL PROYEK
     * (TANPA SDM – SDM ada di progress)
     */
    public function show(Project $project)
    {
        $project->load([
            'client',
            'phases' => fn ($q) => $q->orderBy('urutan'),
        ]);

        return view('project.show', compact('project'));
    }

    /**
     * FORM EDIT PROYEK
     */
    public function edit(Project $project)
    {
        $project->load('phases');
        $clients = Client::orderBy('nama')->get();

        return view('project.edit', compact('project', 'clients'));
    }

    /**
     * UPDATE PROYEK
     */
    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'nama_proyek'     => 'required|string|max:255',
            'client_id'       => 'required|exists:clients,id',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',

            'dokumen'         => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg|max:5120',
            'rab'             => 'nullable|file|mimes:pdf,xls,xlsx|max:5120',

            'tahapan'                => 'required|array|min:1',
            'tahapan.*.nama_tahapan' => 'required|string|max:255',
            'tahapan.*.persen'       => 'required|integer|min:0|max:100',
        ]);

        // validasi total tahapan
        $total = collect($data['tahapan'])->sum(fn ($t) => (int) $t['persen']);
        if ($total !== 100) {
            return back()->withInput()->withErrors([
                'tahapan_total' => "Total persentase tahapan harus 100%. Saat ini: {$total}%",
            ]);
        }

        DB::transaction(function () use ($request, $data, $project) {

            // update dokumen
            if ($request->hasFile('dokumen')) {
                if ($project->dokumen && Storage::disk('public')->exists($project->dokumen)) {
                    Storage::disk('public')->delete($project->dokumen);
                }
                $project->dokumen = $request->file('dokumen')
                    ->store('dokumen_proyek', 'public');
            }

            // update RAB
            if ($request->hasFile('rab')) {
                if ($project->rab_path && Storage::disk('public')->exists($project->rab_path)) {
                    Storage::disk('public')->delete($project->rab_path);
                }
                $project->rab_path = $request->file('rab')
                    ->store('rab_proyek', 'public');
            }

            // update data utama
            $project->update([
                'nama_proyek'     => $data['nama_proyek'],
                'client_id'       => $data['client_id'],
                'tanggal_mulai'   => $data['tanggal_mulai'],
                'tanggal_selesai' => $data['tanggal_selesai'],
            ]);

            // reset & insert ulang tahapan
            ProjectPhase::where('project_id', $project->id)->delete();

            foreach ($data['tahapan'] as $i => $t) {
                ProjectPhase::create([
                    'project_id'   => $project->id,
                    'nama_tahapan' => $t['nama_tahapan'],
                    'persen'       => (int) $t['persen'],
                    'urutan'       => $i + 1,
                ]);
            }
        });

        return redirect()
            ->route('project.index')
            ->with('success', 'Proyek berhasil diperbarui.');
    }
}
