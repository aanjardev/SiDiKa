<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categories;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Categories::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('nama_kategori', 'like', "%{$search}%");
        }

        $sortBy = $request->input('sort_by', 'nama');
        switch ($sortBy) {
            case 'nama_desc':
                $query->orderBy('nama_kategori', 'desc');
                break;
            case 'terbaru':
                $query->orderBy('created_at', 'desc');
                break;
            case 'nama':
            default:
                $query->orderBy('nama_kategori', 'asc');
                break;
        }

        $categories = $query->paginate(10)->withQueryString();
        return view('admin.dataKategori', [
            'categories' => $categories,
            'search_term' => $request->input('search', ''),
            'sort_by' => $sortBy,
        ]);
    }

    public function create()
    {

        return view('admin.inputDataKategori');
    }

    public function store(Request $request)
    {
        // Validasi data kategori
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:50|regex:/^[A-Za-zÀ-ž\s\.,\-]+$/',
        ], [
            'nama_kategori.regex' => 'Nama kategori hanya boleh mengandung huruf, spasi, titik, koma, dan tanda hubung.',
        ]);

        Categories::create($validated);
        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $category = Categories::findOrFail($id);
        $count = $category->usedCount();

        // Cek apakah kategori digunakan oleh produk
        if ($count > 0) {
        return redirect()->route('admin.categories.index')
            ->with('error', "Kategori tidak dapat dihapus karena masih digunakan oleh {$count} produk.");
        }   


        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }

    public function edit(Categories $category)
    {
        // Reuse view 'admin.inputDataKategori' tapi kirim data category
        return view('admin.inputDataKategori', compact('category'));
    }

    public function update(Request $request, $id)
    {
        // Validasi data kategori
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:50|regex:/^[A-Za-zÀ-ž\s\.,\-]+$/',
        ], [
            'nama_kategori.regex' => 'Nama kategori hanya boleh mengandung huruf, spasi, titik, koma, dan tanda hubung.',
        ]);
        $category = Categories::findOrFail($id);
        $category->update($validated);
        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }
}
