<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $data_cabang = Branch::latest()->paginate(10);

        return view('admin.dataCabang', [
            'data_cabang' => $data_cabang
        ]);
    }

    public function create()
    {

        return view('admin.inputDataCabang');
    }
}
