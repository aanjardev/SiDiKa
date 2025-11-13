<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualan';

    protected $guarded = [];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'perusahaan_cabang_id');
    }

    public function details()
    {
        return $this->hasMany(DetailPenjualan::class, 'penjualan_id');
    }
}

