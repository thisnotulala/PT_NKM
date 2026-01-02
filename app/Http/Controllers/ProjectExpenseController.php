<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\Sdm;
use App\Models\Equipment;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProjectExpenseController extends Controller
{
    public function index(Project $project)
    {
        $project->load('client');

        $expenses = ProjectExpense::with(['sdm', 'equipment', 'satuan'])
            ->where('project_id', $project->id)
            ->latest('tanggal')
            ->latest()
            ->get();

        $total = $expenses->sum('nominal');

        return view('project.expenses.index', compact('project', 'expenses', 'total'));
    }

    public function create(Project $project)
    {
        $sdms = Sdm::orderBy('nama')->get();
        $equipment = Equipment::orderBy('nama_alat')->get();

        // sesuaikan kolom nama satuan: nama_satuan / nama
        $satuans = Satuan::orderBy('nama_satuan')->get();

        return view('project.expenses.create', compact('project', 'sdms', 'equipment', 'satuans'));
    }

    public function store(Request $request, Project $project)
    {
        // VALIDASI UMUM (BUKTI WAJIB di CREATE)
        $data = $request->validate([
            'tanggal' => 'required|date',
            'kategori' => 'required|string|max:50',

            // Material (opsional dulu; nanti diwajibkan jika kategori Material)
            'qty' => 'nullable|numeric|min:0.01',
            'satuan_id' => 'nullable|exists:satuans,id',

            'nominal' => 'required|numeric|min:0.01',
            'keterangan' => 'nullable|string|max:255',
            'sdm_id' => 'nullable|exists:sdms,id',
            'equipment_id' => 'nullable|exists:equipment,id',

            // ✅ WAJIB UPLOAD BUKTI
            'bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        // Jika kategori Material => wajib qty & satuan
        if (($data['kategori'] ?? '') === 'Material') {
            $request->validate([
                'qty' => 'required|numeric|min:0.01',
                'satuan_id' => 'required|exists:satuans,id',
            ]);
            $data['qty'] = $request->qty;
            $data['satuan_id'] = $request->satuan_id;
        } else {
            $data['qty'] = null;
            $data['satuan_id'] = null;
        }

        // Validasi tanggal harus dalam rentang proyek
        if ($data['tanggal'] < $project->tanggal_mulai || $data['tanggal'] > $project->tanggal_selesai) {
            return back()->withInput()->withErrors([
                'tanggal' => 'Tanggal pengeluaran harus berada dalam rentang tanggal proyek.'
            ]);
        }

        DB::transaction(function () use ($data, $project, $request) {
            // bukti wajib, langsung store
            $path = $request->file('bukti')->store("bukti_pengeluaran/project_{$project->id}", 'public');

            ProjectExpense::create([
                'project_id' => $project->id,
                'tanggal' => $data['tanggal'],
                'kategori' => $data['kategori'],

                // material fields
                'qty' => $data['qty'],
                'satuan_id' => $data['satuan_id'],

                'nominal' => $data['nominal'],
                'keterangan' => $data['keterangan'] ?? null,
                'sdm_id' => $data['sdm_id'] ?? null,
                'equipment_id' => $data['equipment_id'] ?? null,

                'bukti_path' => $path,
                'created_by' => auth()->id(),
            ]);
        });

        return redirect()->route('project.expenses.index', $project->id)
            ->with('success', 'Pengeluaran berhasil ditambahkan.');
    }

    public function edit(Project $project, ProjectExpense $expense)
    {
        if ($expense->project_id != $project->id) abort(404);

        $sdms = Sdm::orderBy('nama')->get();
        $equipment = Equipment::orderBy('nama_alat')->get();
        $satuans = Satuan::orderBy('nama_satuan')->get();

        return view('project.expenses.edit', compact('project', 'expense', 'sdms', 'equipment', 'satuans'));
    }

    public function update(Request $request, Project $project, ProjectExpense $expense)
    {
        if ($expense->project_id != $project->id) abort(404);

        $data = $request->validate([
            'tanggal' => 'required|date',
            'kategori' => 'required|string|max:50',

            // material (opsional dulu)
            'qty' => 'nullable|numeric|min:0.01',
            'satuan_id' => 'nullable|exists:satuans,id',

            'nominal' => 'required|numeric|min:0.01',
            'keterangan' => 'nullable|string|max:255',
            'sdm_id' => 'nullable|exists:sdms,id',
            'equipment_id' => 'nullable|exists:equipment,id',

            // ✅ OPTIONAL saat update
            'bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        // kategori Material => wajib qty & satuan
        if (($data['kategori'] ?? '') === 'Material') {
            $request->validate([
                'qty' => 'required|numeric|min:0.01',
                'satuan_id' => 'required|exists:satuans,id',
            ]);
            $data['qty'] = $request->qty;
            $data['satuan_id'] = $request->satuan_id;
        } else {
            $data['qty'] = null;
            $data['satuan_id'] = null;
        }

        // Validasi tanggal harus dalam rentang proyek
        if ($data['tanggal'] < $project->tanggal_mulai || $data['tanggal'] > $project->tanggal_selesai) {
            return back()->withInput()->withErrors([
                'tanggal' => 'Tanggal pengeluaran harus berada dalam rentang tanggal proyek.'
            ]);
        }

        // ✅ Opsi A: kalau belum ada bukti sebelumnya dan user tidak upload baru => wajib
        if (!$expense->bukti_path && !$request->hasFile('bukti')) {
            return back()->withInput()->withErrors([
                'bukti' => 'Bukti wajib diupload.'
            ]);
        }

        DB::transaction(function () use ($data, $project, $expense, $request) {

            // upload bukti baru jika ada
            if ($request->hasFile('bukti')) {
                if ($expense->bukti_path && Storage::disk('public')->exists($expense->bukti_path)) {
                    Storage::disk('public')->delete($expense->bukti_path);
                }
                $expense->bukti_path = $request->file('bukti')->store("bukti_pengeluaran/project_{$project->id}", 'public');
            }

            $expense->update([
                'tanggal' => $data['tanggal'],
                'kategori' => $data['kategori'],

                // material
                'qty' => $data['qty'],
                'satuan_id' => $data['satuan_id'],

                'nominal' => $data['nominal'],
                'keterangan' => $data['keterangan'] ?? null,
                'sdm_id' => $data['sdm_id'] ?? null,
                'equipment_id' => $data['equipment_id'] ?? null,

                'bukti_path' => $expense->bukti_path,
            ]);
        });

        return redirect()->route('project.expenses.index', $project->id)
            ->with('success', 'Pengeluaran berhasil diupdate.');
    }

    public function destroy(Project $project, ProjectExpense $expense)
    {
        if ($expense->project_id != $project->id) abort(404);

        if ($expense->bukti_path && Storage::disk('public')->exists($expense->bukti_path)) {
            Storage::disk('public')->delete($expense->bukti_path);
        }

        $expense->delete();

        return back()->with('success', 'Pengeluaran berhasil dihapus.');
    }

    public function pickProject()
    {
        $projects = Project::with('client')
            ->withSum('expenses', 'nominal')
            ->orderByDesc('created_at')
            ->get();

        return view('project.expenses.pick', compact('projects'));
    }
}
