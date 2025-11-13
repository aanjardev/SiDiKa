<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;

class EmployeeController extends Controller
{
    public function index()
    {
        $karyawan = Employee::latest()->paginate(10);

        return view('admin.dataKaryawan', [
            'karyawan' => $karyawan
        ]);
    }

    public function create()
    {

        return view('admin.inputDataKaryawan');
    }

    public function store(Request $request)
    {
        // Validasi dan simpan data karyawan
        // Contoh validasi sederhana
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-zÀ-ž\s\.,\-]+$/'
            ],
            'email' => 'required|email|unique:employees,email',
            'nomor_telepon' => [
                'required',
                'regex:/^(?:\+62|62|0)[0-9]{8,15}$/'
            ],
            'jabatan' => 'required|string|max:50',
        ], [
            'nama.regex' => 'Nama karyawan hanya boleh mengandung huruf, spasi, titik, koma, dan tanda hubung.',
            'nomor_telepon.regex' => 'Nomor telepon harus berupa angka dan diawali dengan 0, 62, atau +62.',
        ]);

        // Simpan data karyawan ke database
        // Employee::create($validated);

        return redirect()->route('admin.employees.index')
                         ->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        return view('admin.inputDataKaryawan');
    }

    public function destroy($id)
    {
        // Hapus data karyawan dari database
        Employee::destroy($id);

        return redirect()->route('admin.employees.index')
                         ->with('success', 'Karyawan berhasil dihapus.');
    }
}
