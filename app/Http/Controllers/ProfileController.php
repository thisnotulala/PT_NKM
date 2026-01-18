<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
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

        // update nama & email
        $user->name = $request->name;
        $user->email = $request->email;

        // update password (kalau diisi)
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors([
                    'current_password' => 'Password lama salah'
                ]);
            }

            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui');
    }
}
