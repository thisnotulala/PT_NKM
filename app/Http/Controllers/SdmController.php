<?php

namespace App\Http\Controllers;

use App\Models\Sdm;
use Illuminate\Http\Request;

class SdmController extends Controller
{
    public function index()
    {
        $sdms = Sdm::orderBy('nama')->get();
        return view('sdm.index', compact('sdms'));
    }

    public function create()
    {
        return view('sdm.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'          => 'required|string|max:255',
            'peran'         => 'required|string|max:255',
            'nomor_telepon' => 'nullable|regex:/^[0-9]+$/|digits_between:8,15',
            'alamat'        => 'nullable|string|max:500',
        ], [
            // nama
            'nama.required' => 'Nama SDM wajib diisi.',
            'nama.string'   => 'Nama SDM harus berupa teks.',
            'nama.max'      => 'Nama SDM maksimal 255 karakter.',

            // peran
            'peran.required' => 'Peran SDM wajib diisi.',
            'peran.string'   => 'Peran SDM harus berupa teks.',
            'peran.max'      => 'Peran SDM maksimal 255 karakter.',

            // nomor telepon
            'nomor_telepon.regex'          => 'Nomor telepon hanya boleh berisi angka.',
            'nomor_telepon.digits_between' => 'Nomor telepon harus terdiri dari 8 sampai 15 digit.',

            // alamat
            'alamat.string' => 'Alamat harus berupa teks.',
            'alamat.max'    => 'Alamat maksimal 500 karakter.',
        ]);

        Sdm::create($validated);

        return redirect()->route('sdm.index')
            ->with('success', 'SDM berhasil ditambahkan.');
    }

    public function edit(Sdm $sdm)
    {
        return view('sdm.edit', compact('sdm'));
    }

    public function update(Request $request, Sdm $sdm)
    {
        $validated = $request->validate([
            'nama'          => 'required|string|max:255',
            'peran'         => 'required|string|max:255',
            'nomor_telepon' => 'required|regex:/^[0-9]+$/|digits_between:8,15',
            'alamat'        => 'nullable|string|max:500',
        ], [
            // nama
            'nama.required' => 'Nama SDM wajib diisi.',
            'nama.string'   => 'Nama SDM harus berupa teks.',
            'nama.max'      => 'Nama SDM maksimal 255 karakter.',

            // peran
            'peran.required' => 'Peran SDM wajib diisi.',
            'peran.string'   => 'Peran SDM harus berupa teks.',
            'peran.max'      => 'Peran SDM maksimal 255 karakter.',

            // nomor telepon
            'nomor_telepon.regex'          => 'Nomor telepon hanya boleh berisi angka.',
            'nomor_telepon.digits_between' => 'Nomor telepon harus terdiri dari 8 sampai 15 digit.',
            'nomor_telepon.required'       => 'Nomor telepon wajib diisi.',

            // alamat
            'alamat.string' => 'Alamat harus berupa teks.',
            'alamat.max'    => 'Alamat maksimal 500 karakter.',
        ]);

        $sdm->update($validated);

        return redirect()->route('sdm.index')
            ->with('success', 'SDM berhasil diperbarui.');
    }
}
