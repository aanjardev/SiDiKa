<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('user');

        // Search by nama
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('nama_lengkap', 'like', "%{$search}%");
        }

        // Filter by jabatan
        if ($request->filled('jabatan') && $request->input('jabatan') !== 'all') {
            $query->where('jabatan', $request->input('jabatan'));
        }

        // Filter by status
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        $employees = $query->latest()->paginate(10)->withQueryString();

        return view('admin.dataKaryawan', [
            'employees' => $employees,
            'search_term' => $request->input('search', ''),
            'selected_jabatan' => $request->input('jabatan', 'all'),
            'selected_status' => $request->input('status', 'all'),
        ]);
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
            'jabatan' => [
                'required',
                'string',
                'max:50',
            ],
            'nomor_telepon' => [
                'required',
                'string',
                'max:15',
                'regex:/^(?:\+62|62|0)[0-9]{8,15}$/'
            ],
            'tanggal_masuk' => 'required|date',
            'gaji' => 'nullable|integer|min:0',
            'status' => 'required|in:aktif,non-aktif',
            'alamat' => 'nullable|string|max:100',
            'tanggal_keluar' => 'nullable|date|after_or_equal:tanggal_masuk',
        ], [
            'nama_lengkap.regex' => 'Nama karyawan hanya boleh mengandung huruf, spasi, titik, koma, dan tanda hubung.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'nomor_telepon.regex' => 'Nomor telepon harus berupa angka dan diawali dengan 0, 62, atau +62.',
        ]);

        // Simpan data karyawan
        Employee::create([
            'nama_lengkap' => $validated['nama_lengkap'],
            'nik' => $validated['nik'],
            'jabatan' => $validated['jabatan'],
            'nomor_telepon' => $validated['nomor_telepon'],
            'tanggal_masuk' => $validated['tanggal_masuk'],
            'gaji' => $validated['gaji'] ?? null,
            'status' => $validated['status'],
            'alamat' => $validated['alamat'] ?? null,
            'tanggal_keluar' => $validated['tanggal_keluar'] ?? null,
        ]);

        return redirect()->route('admin.employees.index')
                         ->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function show($id)
    {
        $employee = Employee::findOrFail($id);
        return view('admin.inputDataKaryawan', compact('employee'))->with('readOnly', true);
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        return view('admin.inputDataKaryawan', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

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
            'jabatan' => [
                'required',
                'string',
                'max:50',
            ],
            'nomor_telepon' => [
                'required',
                'string',
                'max:15',
                'regex:/^(?:\+62|62|0)[0-9]{8,15}$/'
            ],
            'tanggal_masuk' => 'required|date',
            'gaji' => 'nullable|integer|min:0',
            'status' => 'required|in:aktif,non-aktif',
            'alamat' => 'nullable|string|max:100',
            'tanggal_keluar' => 'nullable|date|after_or_equal:tanggal_masuk',
        ], [
            'nama_lengkap.regex' => 'Nama karyawan hanya boleh mengandung huruf, spasi, titik, koma, dan tanda hubung.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'nomor_telepon.regex' => 'Nomor telepon harus berupa angka dan diawali dengan 0, 62, atau +62.',
        ]);

        // Update user
        // $employee->update([
        //     'email' => $validated['email'],
        //     'name' => $validated['nama_lengkap'],
        //     'role' => $validated['jabatan'] === 'Manager' ? 'manager' : 'operasional',
        // ]);

        // Update karyawan
        $employee->update([
            'nama_lengkap' => $validated['nama_lengkap'],
            'nik' => $validated['nik'],
            'jabatan' => $validated['jabatan'],
            'nomor_telepon' => $validated['nomor_telepon'],
            'tanggal_masuk' => $validated['tanggal_masuk'],
            'gaji' => $validated['gaji'] ?? null,
            'status' => $validated['status'],
            'alamat' => $validated['alamat'] ?? null,
            'tanggal_keluar' => $validated['tanggal_keluar'] ?? null,
        ]);

        $redirect = match($request->input('redirect_to')) {
        'pageProfil' => route('admin.profile'), 
         default => route('admin.employees.index'),
    };
        return redirect($redirect)->with('success', 'Karyawan berhasil diperbarui.');
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
