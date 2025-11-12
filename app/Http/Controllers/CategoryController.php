<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categories;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Categories::latest()->paginate(10);
        return view('admin.dataKategori', compact('categories'));
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
