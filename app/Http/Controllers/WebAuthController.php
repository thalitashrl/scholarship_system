<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebAuthController extends Controller
{
    /**
     * Tampilkan formulir login.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            if (Auth::user()->hasRole('mahasiswa')) {
                return redirect('/mahasiswa/dashboard');
            }
            return redirect('/admin/dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses otentikasi login sesi.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            $request->session()->regenerate();

            if ($user->hasRole('mahasiswa')) {
                return redirect()->intended('/mahasiswa/dashboard');
            }

            return redirect()->intended('/admin/dashboard');
        }

        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    /**
     * Proses logout sesi.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
