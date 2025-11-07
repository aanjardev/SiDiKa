<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;

class PenjualanController extends Controller
{
    public function index()
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
