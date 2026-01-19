<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('user.index', compact('users'));
    }

    public function create()
    {
        return view('user.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:site manager,administrasi,kepala lapangan',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
        ], [
            // name
            'name.required' => 'Nama wajib diisi.',
            'name.string'   => 'Nama harus berupa teks.',
            'name.max'      => 'Nama maksimal 100 karakter.',

            // email
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email sudah terdaftar, silakan gunakan email lain.',

            // password
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama dengan password.',
            'password_confirmation.required' => 'Konfirmasi password wajib diisi.',

            'role.required' => 'Hak akses wajib dipilih.',
            'role.in' => 'Hak akses tidak valid.',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('user.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    public function edit(User $user)
    {
        return view('user.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:site manager,administrasi,kepala lapangan',
            'password' => 'nullable|min:6|confirmed',
            'password_confirmation' => 'required_with:password',
        ], [
            // name
            'name.required' => 'Nama wajib diisi.',
            'name.string'   => 'Nama harus berupa teks.',
            'name.max'      => 'Nama maksimal 100 karakter.',

            // email
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email sudah terdaftar, silakan gunakan email lain.',

            // password (opsional)
            'password.min'       => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama dengan password.',
            'password_confirmation.required_with' => 'Konfirmasi password wajib diisi jika mengubah password.',

            'role.required' => 'Hak akses wajib dipilih.',
            'role.in' => 'Hak akses tidak valid.',
        ]);

        $data = $request->only('name', 'email', 'role');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('user.index')
            ->with('success', 'User berhasil diupdate');
    }

}
