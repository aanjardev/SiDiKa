<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $data_pelanggan = Customer::latest()->paginate(10);

        return view('admin.dataPelanggan', [
            'data_pelanggan' => $data_pelanggan
        ]);
    }

    public function create()
    {

        // return view('admin.inputDataPelanggan');
    }
}
