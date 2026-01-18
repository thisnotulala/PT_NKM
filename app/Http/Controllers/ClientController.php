<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::orderBy('nama')->get();
        return view('client.index', compact('clients'));
    }

    public function create()
    {
        return view('client.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'          => 'required|string|max:255',
            'alamat'        => 'nullable|string|max:500',
            'nomor_telepon' => 'required|regex:/^[0-9]+$/|digits_between:8,15',
        ], [
            // nama
            'nama.required' => 'Nama client wajib diisi.',
            'nama.string'   => 'Nama client harus berupa teks.',
            'nama.max'      => 'Nama client maksimal 255 karakter.',

            // alamat
            'alamat.string' => 'Alamat harus berupa teks.',
            'alamat.max'    => 'Alamat maksimal 500 karakter.',

            // nomor telepon
            'nomor_telepon.required'       => 'Nomor telepon wajib diisi.',
            'nomor_telepon.regex'          => 'Nomor telepon hanya boleh berisi angka.',
            'nomor_telepon.digits_between' => 'Nomor telepon harus terdiri dari 8 sampai 15 digit.',
        ]);

        Client::create($request->only([
            'nama',
            'alamat',
            'nomor_telepon'
        ]));

        return redirect()->route('client.index')
            ->with('success', 'Client berhasil ditambahkan');
    }


    public function edit(Client $client)
    {
        return view('client.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'nama'          => 'required|string|max:255',
            'alamat'        => 'nullable|string|max:500',
            'nomor_telepon' => 'required|regex:/^[0-9]+$/|digits_between:8,15',
        ], [
            // nama
            'nama.required' => 'Nama client wajib diisi.',
            'nama.string'   => 'Nama client harus berupa teks.',
            'nama.max'      => 'Nama client maksimal 255 karakter.',

            // alamat
            'alamat.string' => 'Alamat harus berupa teks.',
            'alamat.max'    => 'Alamat maksimal 500 karakter.',

            // nomor telepon
            'nomor_telepon.required'       => 'Nomor telepon wajib diisi.',
            'nomor_telepon.regex'          => 'Nomor telepon hanya boleh berisi angka.',
            'nomor_telepon.digits_between' => 'Nomor telepon harus terdiri dari 8 sampai 15 digit.',
        ]);

        $client->update($request->only([
            'nama',
            'alamat',
            'nomor_telepon'
        ]));

        return redirect()->route('client.index')
            ->with('success', 'Client berhasil diperbarui');
    }


}
