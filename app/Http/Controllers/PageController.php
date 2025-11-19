<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\CatalogSettings;
use App\Models\CatalogBanners;
use App\Models\CatalogPartnerLogo;

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

        $cat_setting = CatalogSettings::first();
        $cat_banners = CatalogBanners::all();
        $cat_partner = CatalogPartnerLogo::all();

        return view('mainPage', compact('latestProducts', 'produkUnggulan', 'cat_banners', 'cat_setting', 'cat_partner'));

    }

    public function about(){
        $cat_setting = CatalogSettings::first();
        return view("AboutStore", compact('cat_setting'));
    }

    public function contact(){
        $cat_setting = CatalogSettings::first();
        return view("contact", compact('cat_setting'));
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

