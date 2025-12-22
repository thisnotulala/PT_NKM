<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ⬅️ WAJIB

class AuthController extends Controller
{
    public function formLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect('/admin/dashboard');
        }

        return back()->withErrors(['email' => 'Login gagal']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
