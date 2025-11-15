<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;
use App\Models\Penjualan;

class PenjualanController extends Controller
{

    public function index()
    {
        // 1. Ambil semua kategori untuk filter dropdown
        $semua_kategori = Kategori::orderBy('nama_kategori', 'asc')->get();

        // 2. Ambil data penjualan, paginasi, dan Eager Load
        $data_penjualan = Penjualan::with([
                                    'customer',
                                    'perusahaan_cabang',
                                    'user',
                                    'detail_penjualan.produk' // <-- PENTING: Ambil item & nama produknya
                                ])
                                ->latest() // Urutkan dari yg terbaru (berdasarkan created_at)
                                ->paginate(10); // Ambil 10 data per halaman

        // 3. Kirim data ke view
        return view('admin.dataPenjualan', [
            'data_penjualan' => $data_penjualan,
            'semua_kategori' => $semua_kategori
        ]);
    }

    public function new()
    {
        $kategori = Kategori::orderBy('nama_kategori', 'asc')->get();

        // // 4. Ambil semua data penjualan
        // // (Kita bisa buat ini lebih kompleks nanti dengan relasi)
        // $penjualan = Penjualan::latest()->get(); // 'latest()' -> urutkan dari yg terbaru

        // 5. Kirim kedua data tersebut ke view
        return view('admin.dataPenjualan', [
            'kategori' => $kategori,
            // 'data_penjualan' => $penjualan
        ]);
    }
}
