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
            'nama_alat'  => 'required|string|max:255',
            'satuan_id'  => 'required|exists:satuans,id',
            'stok'       => 'required|integer|min:0',
            'kondisi'    => 'required|string|max:50',
        ]);

        Equipment::create($data);

        return redirect()->route('equipment.index')->with('success', 'Equipment berhasil ditambahkan.');
    }

    public function edit(Equipment $equipment)
    {
        $satuans = Satuan::orderBy('nama_satuan')->get();
        return view('equipment.edit', compact('equipment', 'satuans'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        $data = $request->validate([
            'nama_alat'  => 'required|string|max:255',
            'satuan_id'  => 'required|exists:satuans,id',
            'stok'       => 'required|integer|min:0',
            'kondisi'    => 'required|string|max:50',
        ]);

        $equipment->update($data);

        return redirect()->route('equipment.index')->with('success', 'Equipment berhasil diupdate.');
    }

    public function destroy(Equipment $equipment)
    {
        if (auth()->user()->role !== 'site manager') {
            abort(403, 'AKSES DITOLAK');
        }

        $equipment->delete();
        return redirect()->route('equipment.index')->with('success', 'Equipment berhasil dihapus.');
    }
    
}
