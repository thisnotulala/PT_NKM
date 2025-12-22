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
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'nomor_telepon' => 'nullable|string|max:50',
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
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'nomor_telepon' => 'nullable|string|max:50',
        ]);

        $client->update($request->only([
            'nama',
            'alamat',
            'nomor_telepon'
        ]));

        return redirect()->route('client.index')
            ->with('success', 'Client berhasil diperbarui');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('client.index')
            ->with('success', 'Client berhasil dihapus');
    }
}
