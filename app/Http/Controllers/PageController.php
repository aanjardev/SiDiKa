<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;

class PageController extends Controller
{
    public function index(){
        $latestProducts = Produk::with('gambarUtama')
            ->where('stok_produk', '>', 0)
            ->latest()
            ->take(5)
            ->get();

        $produkUnggulan = Produk::with('gambarUtama')
            ->where('grade', 'Unggulan')
            ->where('stok_produk', '>', 0)
            ->take(5)
            ->get();

        return view('mainPage', compact('latestProducts', 'produkUnggulan'));

    }

    public function about(){
        return view("AboutStore");
    }

    public function contact(){
        return view("contact");
    }

    public function katalog(){
        return view("product");
    }

    public function admin()
    {
        return redirect()->route('admin.dashboard');
    }

    public function edit(){
        return view("admin.edit");
    }
}

