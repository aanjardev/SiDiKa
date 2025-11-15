<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemPembelian;
use App\Models\Kategori;

class QCController extends Controller
{
    /**
     * Tampilkan halaman daftar item yang menunggu QC.
     */
    public function index()
    {
        // 1. Ambil semua kategori untuk filter dropdown
        $semua_kategori = Kategori::orderBy('nama_kategori', 'asc')->get();

        // 2. Ambil semua item yang 'Deal' DAN statusnya 'menunggu_qc'
        $data_qc = ItemPembelian::with(['pembelian', 'kategori']) // Eager load
            ->where('status_qc', 'menunggu_qc') // <-- INI LOGIKA UTAMA ANDA
            ->whereHas('pembelian', function ($query) {
                $query->where('status_pembelian', 'deal'); // Pastikan dari transaksi 'deal'
            })
            ->latest() // Urutkan dari yg terbaru
            ->paginate(10); // Paginasi 10 item per halaman

        // 3. Kirim data ke view
        return view('admin.dataQC', [
            'data_qc' => $data_qc,
            'semua_kategori' => $semua_kategori
        ]);
    }

    /**
     * Tampilkan halaman/form untuk memPROSES item QC.
     */
    public function edit($id)
    {
        // $item = ItemPembelianDraft::findOrFail($id);
        // return view('admin.dataQC-form', ['item' => $item]);
        return "Ini adalah halaman form Proses QC untuk Item ID: " . $id;
    }

    /**
     * Update item setelah diproses QC.
     */
    public function update(Request $request, $id)
    {
        // (Logika Anda untuk update data, ganti status_qc, dan buat produk baru ada di sini)
    }
}
