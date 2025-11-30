<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        // Manual validation untuk menghindari double error
        if (!$request->email || !$request->password) {
            return back()->withErrors([
                'email' => 'Email dan password harus diisi.',
            ])->onlyInput('email');
        }

        // Cek user exists dan status (sebelum validasi format email)
        $user = \App\Models\User::where('email', $request->email)->first();
        
        if ($user) {
            // Jika user ada, cek status dulu
            if ($user->status === 'pending') {
                return back()->withErrors([
                    'email' => 'Akun Anda belum diaktivasi. Silakan aktivasi akun terlebih dahulu.',
                ])->onlyInput('email');
            }

            // Cek status karyawan terkait
            if ($user->employee && $user->employee->status === 'non-aktif') {
                // Auto-deactivate user jika karyawan non-aktif
                $user->update(['status' => 'inactive']);
                
                return back()->withErrors([
                    'email' => 'Anda sudah tidak memiliki akses untuk login. Hubungi manager untuk informasi lebih lanjut.',
                ])->onlyInput('email');
            }

            // Cek jika user status inactive (manual set)
            if ($user->status === 'inactive') {
                return back()->withErrors([
                    'email' => 'Anda sudah tidak memiliki akses untuk login. Hubungi manager untuk informasi lebih lanjut.',
                ])->onlyInput('email');
            }
        }

        // Validasi format email hanya jika user tidak ada atau status ok
        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            return back()->withErrors([
                'email' => 'Format email tidak valid.',
            ])->onlyInput('email');
        }

        // Jika user tidak ada setelah cek format
        if (!$user) {
            return back()->withErrors([
                'email' => 'Email atau Password salah.',
            ])->onlyInput('email');
        }

        // Coba untuk login dengan credentials
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        // Jika login gagal (password salah)
        return back()->withErrors([
            'email' => 'Email atau Password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
