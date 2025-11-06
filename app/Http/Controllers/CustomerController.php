<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        return view('admin.dataPelanggan');
    }

    public function create()
    {

        // return view('admin.inputDataPelanggan');
    }
}
