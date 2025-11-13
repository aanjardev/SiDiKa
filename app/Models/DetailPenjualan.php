<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    use HasFactory;

    protected $table = 'detail_penjualan';

    protected $guarded = [];

    public function sale()
    {
        return $this->belongsTo(Penjualan::class, 'penjualan_id');
    }

    public function product()
    {
        return $this->belongsTo(AdminProduct::class, 'produk_id');
    }
}

