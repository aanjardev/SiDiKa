<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('user')->latest()->paginate(10);
        return view('admin.dataKaryawan', compact('employees'));
    }

    public function create()
    {
        return view('admin.inputDataKaryawan');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-zÀ-ž\s\.,\-]+$/'
            ],
            'nik' => [
                'required',
                'string',
                'max:20',
                'unique:karyawan,nik'
            ],
            'jabatan' => 'required|in:Manager,Staff Operasional',
            'email' => 'required|email|unique:users,email',
            'nomor_telepon' => [
                'required',
                'string',
                'max:15',
                'regex:/^(?:\+62|62|0)[0-9]{8,15}$/'
            ],
            'tanggal_masuk' => 'required|date',
            'gaji' => 'required|integer|min:0',
            'status' => 'required|in:aktif,non-aktif',
            'alamat' => 'required|string|max:100',
        ], [
            'nama_lengkap.regex' => 'Nama karyawan hanya boleh mengandung huruf, spasi, titik, koma, dan tanda hubung.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'email.unique' => 'Email sudah terdaftar.',
            'nomor_telepon.regex' => 'Nomor telepon harus berupa angka dan diawali dengan 0, 62, atau +62.',
            'jabatan.in' => 'Jabatan harus dipilih dari opsi yang tersedia.',
        ]);

        // Buat user terlebih dahulu
        $user = User::create([
            'username' => Str::slug($validated['nama_lengkap']) . '_' . Str::random(4),
            'email' => $validated['email'],
            'password' => Hash::make('password123'), // Default password, bisa diganti
            'name' => $validated['nama_lengkap'],
            'role' => $validated['jabatan'] === 'Manager' ? 'manager' : 'operasional',
        ]);

        // Simpan data karyawan
        Employee::create([
            'user_id' => $user->id,
            'nama_lengkap' => $validated['nama_lengkap'],
            'nik' => $validated['nik'],
            'jabatan' => $validated['jabatan'],
            'nomor_telepon' => $validated['nomor_telepon'],
            'tanggal_masuk' => $validated['tanggal_masuk'],
            'gaji' => $validated['gaji'],
            'status' => $validated['status'],
            'alamat' => $validated['alamat'],
        ]);

        return redirect()->route('admin.employees.index')
                         ->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function show($id)
    {
        $employee = Employee::with('user')->findOrFail($id);
        return view('admin.inputDataKaryawan', compact('employee'))->with('readOnly', true);
    }

    public function edit($id)
    {
        $employee = Employee::with('user')->findOrFail($id);
        return view('admin.inputDataKaryawan', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::with('user')->findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-zÀ-ž\s\.,\-]+$/'
            ],
            'nik' => [
                'required',
                'string',
                'max:20',
                'unique:karyawan,nik,' . $id
            ],
            'jabatan' => 'required|in:Manager,Staff Operasional',
            'email' => 'required|email|unique:users,email,' . $employee->user_id,
            'nomor_telepon' => [
                'required',
                'string',
                'max:15',
                'regex:/^(?:\+62|62|0)[0-9]{8,15}$/'
            ],
            'tanggal_masuk' => 'required|date',
            'gaji' => 'required|integer|min:0',
            'status' => 'required|in:aktif,non-aktif',
            'alamat' => 'required|string|max:100',
        ], [
            'nama_lengkap.regex' => 'Nama karyawan hanya boleh mengandung huruf, spasi, titik, koma, dan tanda hubung.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'email.unique' => 'Email sudah terdaftar.',
            'nomor_telepon.regex' => 'Nomor telepon harus berupa angka dan diawali dengan 0, 62, atau +62.',
            'jabatan.in' => 'Jabatan harus dipilih dari opsi yang tersedia.',
        ]);

        // Update user
        $employee->user->update([
            'email' => $validated['email'],
            'name' => $validated['nama_lengkap'],
            'role' => $validated['jabatan'] === 'Manager' ? 'manager' : 'operasional',
        ]);

        // Update karyawan
        $employee->update([
            'nama_lengkap' => $validated['nama_lengkap'],
            'nik' => $validated['nik'],
            'jabatan' => $validated['jabatan'],
            'nomor_telepon' => $validated['nomor_telepon'],
            'tanggal_masuk' => $validated['tanggal_masuk'],
            'gaji' => $validated['gaji'],
            'status' => $validated['status'],
            'alamat' => $validated['alamat'],
        ]);

        return redirect()->route('admin.employees.index')
                         ->with('success', 'Karyawan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        
        // Hapus user terkait (akan terhapus otomatis karena cascade)
        if ($employee->user) {
            $employee->user->delete();
        }
        
        $employee->delete();

        return redirect()->route('admin.employees.index')
                         ->with('success', 'Karyawan berhasil dihapus.');
    }
}
