<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\GambarProduk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class AdminProductController extends Controller
{
    public function index()
    {
        $products = Produk::with(['gambar', 'gambarUtama', 'kategori'])
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

        $kategori = Kategori::orderBy('nama_kategori', 'asc')->get();

        return view('admin.inputDataProduk', [
            'semua_kategori' => $kategori,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk' => ['required', 'string', 'max:200'],
            'kode_sku' => ['required', 'string', 'max:20', 'unique:produk,kode_sku'],
            'id_kategori' => ['required', 'exists:kategori,id'],
            'harga_jual' => ['nullable', 'integer', 'min:0'],
            'harga_beli' => ['nullable', 'integer', 'min:0'],
            'harga_servis' => ['nullable', 'integer', 'min:0'],
            'stok_produk' => ['nullable', 'integer', 'min:0'],
            'deskripsi_produk' => ['nullable', 'string'],
            'status' => ['required', 'in:Second,Baru'],
            'grade' => ['required', 'in:Unggulan,Standar,Minus'],
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['image', 'max:5120'],
        ]);

        DB::beginTransaction();

        try {
            $produk = Produk::create([
                'nama_produk' => $validated['nama_produk'],
                'kode_sku' => $validated['kode_sku'],
                'id_kategori' => $validated['id_kategori'],
                'harga_jual' => $validated['harga_jual'] ?? null,
                'harga_beli' => $validated['harga_beli'] ?? null,
                'harga_servis' => $validated['harga_servis'] ?? null,
                'stok_produk' => $validated['stok_produk'] ?? null,
                'deskripsi_produk' => $validated['deskripsi_produk'] ?? null,
                'status' => $validated['status'],
                'grade' => $validated['grade'],
            ]);

            foreach ($validated['images'] as $index => $image) {
                $extension = $image->getClientOriginalExtension();
                $filename = Str::uuid()->toString() . ($extension ? '.' . $extension : '');
                $path = Storage::disk('r2')->putFileAs('product-images', $image, $filename, [
                    'visibility' => 'public',
                ]);

                GambarProduk::create([
                    'id_produk' => $produk->id,
                    'path_gambar' => $path,
                    'is_main' => $index === 0,
                ]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan produk: ' . $th->getMessage()]);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan dan gambar tersimpan di Cloudflare R2.');
    }
}
