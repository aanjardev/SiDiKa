<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\ItemPembelian;
use Illuminate\Http\Request;

class PembelianController extends Controller
{
    public function index()
    {
        $data_pembelian = Pembelian::with(['customer', 'perusahaan_cabang', 'user', 'item_pembelian_draft'])
                                    ->latest()
                                    ->paginate(10);

        return view('admin.dataPembelian', [
            'data_pembelian' => $data_pembelian
        ]);
    }

}
