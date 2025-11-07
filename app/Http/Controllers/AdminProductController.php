<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminProduct;
class AdminProductController extends Controller
{
    public function index()
    {
        $products = AdminProduct::orderBy('updated_at', 'desc')->paginate(10); // 10 item per page
        return view('admin.dataProduk', compact('products'));
    }

    public function create()
    {

        return view('admin.inputDataProduk');
    }
}
