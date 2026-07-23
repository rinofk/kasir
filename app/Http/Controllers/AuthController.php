<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->hasRole('admin') 
                ? redirect()->route('dashboard') 
                : redirect()->route('pos');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email atau Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $loginInput = trim($request->email);

        // Find user by exact email or email prefix (e.g. 'lonkwandi' matching 'lonkwandi@gmail.com')
        $user = \App\Models\User::where('email', $loginInput)
            ->orWhere('email', 'like', $loginInput . '@%')
            ->first();

        $emailToAttempt = $user ? $user->email : $loginInput;

        if (Auth::attempt(['email' => $emailToAttempt, 'password' => $request->password], $request->remember)) {
            if (!Auth::user()->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            return Auth::user()->hasRole('admin')
                ? redirect()->route('dashboard')
                : redirect()->route('pos');
        }

        return back()->withErrors([
            'email' => 'Username/Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
