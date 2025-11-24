<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\GambarProduk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Helpers\ImageUpload;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::with(['gambar', 'gambarUtama', 'kategori'])
            ->orderBy('updated_at', 'desc');

        // Search by nama produk or SKU
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                    ->orWhere('kode_sku', 'like', "%{$search}%");
            });
        }

        // Filter by kategori
        if ($request->filled('kategori') && $request->input('kategori') !== 'all') {
            $query->where('id_kategori', $request->input('kategori'));
        }

        // Sort by
        $sortBy = $request->input('sort_by', 'updated_at');
        $sortOrder = $request->input('sort_order', 'desc');

        if ($sortBy === 'nama') {
            $query->orderBy('nama_produk', $sortOrder);
        } else {
            $query->orderBy('updated_at', $sortOrder);
        }

        $products = $query->paginate(10)->withQueryString();

        // Ambil data kategori untuk filter
        $kategori = Kategori::orderBy('nama_kategori', 'asc')->get();

        return view('admin.dataProduk', [
            'products' => $products,
            'semua_kategori' => $kategori,
            'search_term' => $request->input('search', ''),
            'selected_kategori' => $request->input('kategori', 'all'),
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
        ]);
    }

    /**
     * Tampilkan daftar produk yang belum memiliki gambar (perlu diupload foto)
     */
    public function photos(Request $request)
    {
        $query = Produk::with('kategori')
            ->whereDoesntHave('gambar');

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('nama_produk', 'like', "%{$s}%")
                    ->orWhere('kode_sku', 'like', "%{$s}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('id_kategori', $request->input('kategori'));
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(10);

        // AJAX support: return partial HTML for tbody + pagination
        if ($request->ajax() || $request->wantsJson()) {
            $table_html = view('admin.partials.photo_product_rows', ['products' => $products])->render();
            $pagination_html = $products->hasPages() ? $products->appends($request->query())->links('pagination::bootstrap-5')->render() : '';
            return response()->json([
                'table_html' => $table_html,
                'pagination_html' => $pagination_html,
            ]);
        }

        $kategori = Kategori::orderBy('nama_kategori', 'asc')->get();

        return view('admin.dataFotoProduk', [
            'products' => $products,
            'semua_kategori' => $kategori,
            'search_term' => $request->input('search', ''),
            'selected_kategori' => $request->input('kategori', ''),
        ]);
    }

    /**
     * Show upload form for product photos
     */
    public function photosUpload($id)
    {
        // load the photos relation (alias added on the Produk model)
        $product = Produk::with('photos')->findOrFail($id);
        return view('admin.uploadProductPhotos', [
            'product' => $product,
        ]);
    }

    public function photosUploadStore(Request $request, $id)
    {
        $product = Produk::findOrFail($id);

        $validated = $request->validate([
            'images' => ['sometimes', 'array', 'min:1'],
            'images.*' => ['image', 'max:5120'],
            'main_image' => ['nullable', 'string'],
        ]);

        DB::beginTransaction();
        try {
            $images = $request->file('images', []);
            $createdIds = [];
            $hasMain = $product->gambarUtama()->exists();

            foreach ($images as $index => $image) {

                // 🔥 Pakai HELPER baru kita (compress + webp + hash)
                $path = ImageUpload::upload($image, "product/{$product->id}");

                $g = \App\Models\GambarProduk::create([
                    'id_produk' => $product->id,
                    'path_gambar' => $path,
                    'is_main' => false,
                ]);

                $createdIds[] = $g->id;
            }

            // --- HANDLE SET MAIN IMAGE ---
            if (!empty($validated['main_image'])) {
                $main = $validated['main_image'];

                if (str_starts_with($main, 'existing_')) {
                    $idToSet = intval(substr($main, 9));

                    GambarProduk::where('id_produk', $product->id)
                        ->update(['is_main' => false]);

                    GambarProduk::where('id_produk', $product->id)
                        ->where('id', $idToSet)
                        ->update(['is_main' => true]);
                } elseif (str_starts_with($main, 'new_')) {
                    $idx = intval(substr($main, 4));
                    if (isset($createdIds[$idx])) {
                        GambarProduk::where('id_produk', $product->id)
                            ->update(['is_main' => false]);

                        GambarProduk::where('id_produk', $product->id)
                            ->where('id', $createdIds[$idx])
                            ->update(['is_main' => true]);
                    }
                }
            } else {
                // kalau tidak memilih main → atur otomatis
                if (!$hasMain && !empty($createdIds)) {
                    GambarProduk::where('id', $createdIds[0])->update(['is_main' => true]);
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()->withErrors([
                'error' => 'Gagal mengunggah gambar: ' . $th->getMessage()
            ]);
        }

        return redirect()
            ->route('admin.products.photos')
            ->with('success', 'Gambar berhasil diunggah.');
    }

    /**
     * Single AJAX upload for one image. Returns JSON with image data.
     */
    public function photosUploadSingle(Request $request, $id)
    {
        $product = Produk::findOrFail($id);

        $validated = $request->validate([
            'image' => ['required', 'image', 'max:5120'],
        ]);

        DB::beginTransaction();
        try {

            $image = $request->file('image');

            $path = ImageUpload::upload($image, "product/{$product->id}");

            $hasMain = $product->gambarUtama()->exists();

            $gambar = GambarProduk::create([
                'id_produk' => $product->id,
                'path_gambar' => $path,
                'is_main' => !$hasMain,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'image' => [
                    'id' => $gambar->id,
                    'url' => $gambar->url,
                    'is_main' => $gambar->is_main,
                ],
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Set a given image as the main image for a product.
     */
    public function setMainPhoto(Request $request, $productId, $imageId)
    {
        $product = Produk::findOrFail($productId);
        $gambar = GambarProduk::where('id_produk', $product->id)->where('id', $imageId)->firstOrFail();

        DB::beginTransaction();
        try {
            // unset existing main
            GambarProduk::where('id_produk', $product->id)->update(['is_main' => false]);
            $gambar->is_main = true;
            $gambar->save();
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }

    /**
     * Delete a product image
     */
    public function deletePhoto(Request $request, $productId, $imageId)
    {
        $product = Produk::findOrFail($productId);
        $gambar = GambarProduk::where('id_produk', $product->id)->where('id', $imageId)->firstOrFail();

        DB::beginTransaction();
        try {
            $gambar->delete();
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }

    public function create()
    {

        $kategori = Kategori::orderBy('nama_kategori', 'asc')->get();

        return view('admin.inputDataProduk', [
            'semua_kategori' => $kategori,
            'product' => null,
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
            'images' => ['nullable', 'array', 'min:1'],
            'images.*' => ['image', 'max:5120'],
        ]);

        DB::beginTransaction();

        try {
            // 1. Create product
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

            // Prefix folder per product
            $prefix = 'product/' . $produk->id;

            // 2. Upload & save images
            foreach ($validated['images'] as $index => $image) {

                $ext = $image->getClientOriginalExtension();
                $filename = Str::uuid() . '.' . $ext;

                $path = Storage::disk('r2')->putFileAs($prefix, $image, $filename, [
                    'visibility' => 'public',
                    'CacheControl' => 'public, max-age=31536000, immutable'
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
            ->with('success', 'Produk berhasil ditambahkan');
    }


    public function edit(Produk $product)
    {
        $kategori = Kategori::orderBy('nama_kategori', 'asc')->get();
        $product->load(['gambar', 'gambarUtama']);

        return view('admin.inputDataProduk', [
            'semua_kategori' => $kategori,
            'product' => $product,
        ]);
    }

    public function update(Request $request, Produk $product)
    {
        $validated = $request->validate([
            'nama_produk'      => ['required', 'string', 'max:200'],
            'kode_sku'         => ['required', 'string', 'max:20', Rule::unique('produk', 'kode_sku')->ignore($product->id)],
            'id_kategori'      => ['required', 'exists:kategori,id'],
            'harga_jual'       => ['nullable', 'integer', 'min:0'],
            'harga_beli'       => ['nullable', 'integer', 'min:0'],
            'harga_servis'     => ['nullable', 'integer', 'min:0'],
            'stok_produk'      => ['nullable', 'integer', 'min:0'],
            'deskripsi_produk' => ['nullable', 'string'],
            'status'           => ['required', 'in:Second,Baru'],
            'grade'            => ['required', 'in:Unggulan,Standar,Minus'],

            // file baru
            'images'   => ['nullable', 'array'],
            'images.*' => ['image', 'max:5120'],

            // hidden input dari gambar lama
            'remove_images'   => ['array'],
            'remove_images.*' => ['nullable', 'integer'],
        ]);

        DB::beginTransaction();

        try {
            $product->update([
                'nama_produk'      => $validated['nama_produk'],
                'kode_sku'         => $validated['kode_sku'],
                'id_kategori'      => $validated['id_kategori'],
                'harga_jual'       => $validated['harga_jual'] ?? null,
                'harga_beli'       => $validated['harga_beli'] ?? null,
                'harga_servis'     => $validated['harga_servis'] ?? null,
                'stok_produk'      => $validated['stok_produk'] ?? null,
                'deskripsi_produk' => $validated['deskripsi_produk'] ?? null,
                'status'           => $validated['status'],
                'grade'            => $validated['grade'],
            ]);

            $removeIds = array_filter($request->remove_images ?? [], fn($v) => !empty($v));

            if (!empty($removeIds)) {

                $imagesToDelete = GambarProduk::whereIn('id', $removeIds)
                    ->where('id_produk', $product->id)
                    ->get();

                foreach ($imagesToDelete as $img) {

                    // hapus file di Cloudflare R2
                    if (Storage::disk('r2')->exists($img->path_gambar)) {
                        Storage::disk('r2')->delete($img->path_gambar);
                    }

                    // hapus database entry
                    $img->delete();
                }
            }

            $newImages = $request->file('images', []);
            $hasMain = $product->gambarUtama()->exists();

            // folder milik produk ini
            $prefix = 'product/' . $product->id;

            foreach ($newImages as $index => $image) {

                $ext = $image->getClientOriginalExtension();
                $filename = Str::uuid() . '.' . $ext;

                $path = Storage::disk('r2')->putFileAs($prefix, $image, $filename, [
                    'visibility' => 'public',
                    'CacheControl' => 'public, max-age=31536000, immutable'
                ]);

                GambarProduk::create([
                    'id_produk'   => $product->id,
                    'path_gambar' => $path,
                    'is_main'     => $hasMain ? false : $index === 0,
                ]);
            }
            if (!$product->gambarUtama()->exists()) {
                $first = $product->gambar()->first();
                if ($first) {
                    $first->update(['is_main' => true]);
                }
            }
            DB::commit();
        } catch (\Throwable $th) {

            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $th->getMessage()]);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $product)
    {
        DB::beginTransaction();

        try {
            $product->load('gambar');

            foreach ($product->gambar as $gambar) {
                if (Storage::disk('r2')->exists($gambar->path_gambar)) {
                    Storage::disk('r2')->delete($gambar->path_gambar);
                }
                $gambar->delete();
            }

            $product->delete();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menghapus produk: ' . $th->getMessage()]);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
