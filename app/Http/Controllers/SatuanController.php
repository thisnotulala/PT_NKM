<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Satuan;

class SatuanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:site manager,administrasi']);
    }

    public function index()
    {
        $satuans = Satuan::orderByDesc('created_at')->get();
        return view('satuan.index', compact('satuans'));
    }

    public function create()
    {
        return view('satuan.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_satuan' => 'required|string|max:50|unique:satuans,nama_satuan',
        ], [
            'nama_satuan.required' => 'Nama satuan wajib diisi.',
            'nama_satuan.string'   => 'Nama satuan harus berupa teks.',
            'nama_satuan.max'      => 'Nama satuan maksimal 50 karakter.',
            'nama_satuan.unique'   => 'Nama satuan sudah ada, silakan gunakan nama yang lain.',
        ]);

        Satuan::create($data);

        return redirect()->route('satuan.index')
            ->with('success', 'Satuan berhasil ditambahkan.');
    }

    public function edit(Satuan $satuan)
    {
        return view('satuan.edit', compact('satuan'));
    }

    public function update(Request $request, Satuan $satuan)
    {
        $data = $request->validate([
            'nama_satuan' => 'required|string|max:50|unique:satuans,nama_satuan,' . $satuan->id,
        ], [
            'nama_satuan.required' => 'Nama satuan wajib diisi.',
            'nama_satuan.string'   => 'Nama satuan harus berupa teks.',
            'nama_satuan.max'      => 'Nama satuan maksimal 50 karakter.',
            'nama_satuan.unique'   => 'Nama satuan sudah ada, silakan gunakan nama yang lain.',
        ]);

        $satuan->update($data);

        return redirect()->route('satuan.index')
            ->with('success', 'Satuan berhasil diperbarui.');
    }
}
