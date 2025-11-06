<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        return view('admin.dataCabang');
    }

    public function create()
    {

        return view('admin.inputDataCabang');
    }
}
