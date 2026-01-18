<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Satuan;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipment = Equipment::with('satuan')->latest()->get();
        return view('equipment.index', compact('equipment'));
    }

    public function create()
    {
        $satuans = Satuan::orderBy('nama_satuan')->get();
        return view('equipment.create', compact('satuans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_alat' => 'required|string|max:255',
            'satuan_id' => 'required|integer|exists:satuans,id',
            'stok'      => 'required|integer|min:0',
            'kondisi'   => 'required|string|max:50',
        ], [
            'nama_alat.required' => 'Nama alat wajib diisi.',
            'nama_alat.string'   => 'Nama alat harus berupa teks.',
            'nama_alat.max'      => 'Nama alat maksimal 255 karakter.',

            'satuan_id.required' => 'Satuan wajib dipilih.',
            'satuan_id.integer'  => 'Satuan tidak valid.',
            'satuan_id.exists'   => 'Satuan yang dipilih tidak ditemukan.',

            'stok.required' => 'Stok wajib diisi.',
            'stok.integer'  => 'Stok harus berupa angka bulat.',
            'stok.min'      => 'Stok tidak boleh kurang dari 0.',

            'kondisi.required' => 'Kondisi wajib diisi.',
            'kondisi.string'   => 'Kondisi harus berupa teks.',
            'kondisi.max'      => 'Kondisi maksimal 50 karakter.',
        ]);

        Equipment::create($data);

        return redirect()->route('equipment.index')
            ->with('success', 'Equipment berhasil ditambahkan.');
    }

    public function edit(Equipment $equipment)
    {
        $satuans = Satuan::orderBy('nama_satuan')->get();
        return view('equipment.edit', compact('equipment', 'satuans'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        $data = $request->validate([
            'nama_alat' => 'required|string|max:255',
            'satuan_id' => 'required|integer|exists:satuans,id',
            'stok'      => 'required|integer|min:0',
            'kondisi'   => 'required|string|max:50',
        ], [
            'nama_alat.required' => 'Nama alat wajib diisi.',
            'nama_alat.string'   => 'Nama alat harus berupa teks.',
            'nama_alat.max'      => 'Nama alat maksimal 255 karakter.',

            'satuan_id.required' => 'Satuan wajib dipilih.',
            'satuan_id.integer'  => 'Satuan tidak valid.',
            'satuan_id.exists'   => 'Satuan yang dipilih tidak ditemukan.',

            'stok.required' => 'Stok wajib diisi.',
            'stok.integer'  => 'Stok harus berupa angka bulat.',
            'stok.min'      => 'Stok tidak boleh kurang dari 0.',

            'kondisi.required' => 'Kondisi wajib diisi.',
            'kondisi.string'   => 'Kondisi harus berupa teks.',
            'kondisi.max'      => 'Kondisi maksimal 50 karakter.',
        ]);

        $equipment->update($data);

        return redirect()->route('equipment.index')
            ->with('success', 'Equipment berhasil diupdate.');
    }

    
}
