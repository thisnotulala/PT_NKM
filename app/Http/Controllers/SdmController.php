<?php

namespace App\Http\Controllers;

use App\Models\Sdm;
use Illuminate\Http\Request;

class SdmController extends Controller
{
    public function index()
    {
        $sdms = Sdm::latest()->get();
        return view('sdm.index', compact('sdms'));
    }

    public function create()
    {
        return view('sdm.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'peran' => 'required|string|max:255',
            'nomor_telepon' => 'nullable|string|max:30',
            'alamat' => 'nullable|string',
        ]);

        Sdm::create($validated);

        return redirect()->route('sdm.index')->with('success', 'SDM berhasil ditambahkan.');
    }

    public function edit(Sdm $sdm)
    {
        return view('sdm.edit', compact('sdm'));
    }

    public function update(Request $request, Sdm $sdm)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'peran' => 'required|string|max:255',
            'nomor_telepon' => 'nullable|string|max:30',
            'alamat' => 'nullable|string',
        ]);

        $sdm->update($validated);

        return redirect()->route('sdm.index')->with('success', 'SDM berhasil diupdate.');
    }

}
