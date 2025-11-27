<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
    protected $table = 'perusahaan_cabang';
    protected $fillable = [
        'nama',
        'alamat',
        'nomor_telepon',
        'link_maps',
        'jam_buka',
        'jam_tutup',
    ];

    public function sales()
    {
        return $this->hasMany(Penjualan::class, 'perusahaan_cabang_id');
    }

    public function purchases()
    {
        return $this->hasMany(Pembelian::class, 'perusahaan_cabang_id');
    }
}
