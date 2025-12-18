<?php

namespace App\Http\Controllers;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\CatalogSettings;

use Illuminate\Http\Request;


class ProductController extends Controller
{

    public function show($id)
    {
        // Ambil produk dengan relasi gambar dan kategori
        $produk = Produk::with(['gambar', 'kategori'])->findOrFail($id);
        $cat_setting = CatalogSettings::first();

        return view('product-detail', compact('produk', 'cat_setting'));
    }

    public function index(Request $request)
        {
        // Ambil semua kategori untuk dropdown
        $kategoris = Kategori::all();

        // Ambil parameter dari request
        $search = $request->query('search');
        $kategoriFilter = $request->query('kategori');
        $sort = $request->query('sort', 'terbaru'); // Default sort: terbaru

        // Inisialisasi query produk
        $query = Produk::with(['gambarUtama', 'kategori']);

        // Filter pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'LIKE', '%' . $search . '%')
                  ->orWhere('kode_sku', 'LIKE', '%' . $search . '%')
                  ->orWhere('deskripsi_produk', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('kategori', function ($qKategori) use ($search) {
                      $qKategori->where('nama_kategori', 'LIKE', '%' . $search . '%');
                  });
            });
        }

        // Filter kategori
        if ($kategoriFilter && $kategoriFilter !== '') {
            $query->where('id_kategori', $kategoriFilter);
        }

        // Filter sort
        switch ($sort) {
            case 'termurah':
                $query->orderBy('harga_jual', 'asc');
                break;
            case 'termahal':
                $query->orderBy('harga_jual', 'desc');
                break;
            case 'rekomendasi':
                $query->where('grade', 'Unggulan')
                    ->orderBy('created_at', 'desc');
                break;
            case 'terbaru':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Pagination (15 produk per halaman)
        $products = $query->paginate(16);

        return view('product', compact('products', 'kategoris', 'search', 'kategoriFilter', 'sort'));
        }
}
