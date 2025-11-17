<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Pastikan untuk meng-import Auth
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Role yang diizinkan (misal: 'manajer', 'operasional')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Periksa apakah pengguna sudah login
        if (!Auth::check()) {
            // Jika belum, arahkan ke halaman login
            return redirect('/login'); // Sesuaikan dengan route login Anda
        }

        // 2. Dapatkan role pengguna yang sedang login
        // Pastikan Anda memiliki kolom 'role' (atau sesuaikan namanya) di tabel users/employees Anda.
        $userRole = Auth::user()->role;

        // 3. Jika rolenya 'manajer', manajer bisa akses semuanya
        if ($userRole == 'manager') {
            return $next($request);
        }

        // 4. Jika bukan manajer, periksa apakah rolenya (operasional) ada di daftar $roles yang diizinkan
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // 5. Jika tidak diizinkan, kembalikan ke halaman '403 Forbidden' atau dashboard
        // abort(403, 'ANDA TIDAK MEMILIKI HAK AKSES');

        // Atau kembali ke halaman sebelumnya dengan pesan error
        return back()->with('error', 'Anda tidak memiliki hak akses untuk halaman ini.');
    }
}
