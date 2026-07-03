<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session('user_id')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:3', 'max:100'],
            'tahun'    => ['required', 'integer', 'min:2020', 'max:2030'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'tahun.required'    => 'Tahun anggaran wajib dipilih.',
        ]);

        $key = 'login.' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->with('error', "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.");
        }

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key, 300);
            return back()->with('error', 'Username atau password salah.')->withInput(['username' => $request->username, 'tahun' => $request->tahun]);
        }

        RateLimiter::clear($key);

        session([
            'user_id'    => $user->id,
            'user_name'  => $user->name,
            'user_level' => $user->level,
            'tahun'      => (int) $request->tahun,
        ]);

        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Selamat datang, ' . $user->name . '!');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        $request->session()->regenerate();
        return redirect()->route('login')->with('success', 'Anda telah keluar dari sistem.');
    }
}
