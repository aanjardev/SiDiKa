<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PermissionsController extends Controller
{
    public function index()
    {
        // Join users dan karyawan
        $user_data = DB::table('users')
            ->join('karyawan', 'users.id', '=', 'karyawan.id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.role',
                'karyawan.status'
            )
            ->get();

        return view('admin.permissions', compact('user_data'));
    }

    public function create()
    {
        $karyawan_data = DB::table('karyawan')
            ->whereNotIn('id', DB::table('users')->pluck('id'))
            ->where('status', 'aktif')
            ->get();
        return view('admin.inputPermissions', compact('karyawan_data'));
    }


    public function store(Request $request){
    $request->validate([
        'password' => 'required|min:6',
        'email' => 'required|email|unique:users,email'
    ]);

    $karyawan = Employee::findOrFail($request->karyawan_name);

    User::create([
        'id' => $karyawan->id,
        'name' => $karyawan->nama_lengkap,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $karyawan->jabatan
    ]);

    return redirect()->route('admin.permissions')->with('success', 'User berhasil dibuat!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $karyawan_data = Employee::whereNotIn('id', User::pluck('id'))
                                ->orWhere('id', $id) // agar user saat ini tetap muncul
                                ->where('status', 'aktif')
                                ->get();
        return view('admin.inputPermissions', compact('user', 'karyawan_data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'password' => 'nullable|min:6',
            'email' => 'required|email',
        ]);

        $user = User::findOrFail($id);

        $user->update([
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        return redirect()->route('admin.permissions')->with('success', 'User berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.permissions')->with('success', 'User berhasil dihapus!');
    }

}
