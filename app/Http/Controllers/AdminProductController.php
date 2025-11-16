<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Kategori;
class AdminProductController extends Controller
{
    public function index()
    {
        $products = Produk::with(['gambar', 'kategori'])
                        ->orderBy('updated_at', 'desc')
                        ->paginate(10); // 10 item per page

        // 5. Ambil data kategori untuk filter
        $kategori = Kategori::orderBy('nama_kategori', 'asc')->get();

        return view('admin.dataProduk', [
            'products' => $products,
            'semua_kategori' => $kategori
        ]);
    }

    public function create()
    {

        return view('admin.inputDataProduk');
    }
}
