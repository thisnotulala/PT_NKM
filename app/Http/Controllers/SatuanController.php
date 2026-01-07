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
        $satuans = Satuan::latest()->get();
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
        ]);

        Satuan::create($data);

        return redirect()->route('satuan.index')->with('success', 'Satuan berhasil ditambahkan.');
    }

    public function edit(Satuan $satuan)
    {
        return view('satuan.edit', compact('satuan'));
    }

    public function update(Request $request, Satuan $satuan)
    {
        $data = $request->validate([
            'nama_satuan' => 'required|string|max:50|unique:satuans,nama_satuan,' . $satuan->id,
        ]);

        $satuan->update($data);

        return redirect()->route('satuan.index')->with('success', 'Satuan berhasil diupdate.');
    }
}
