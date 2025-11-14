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
    ];

    public function sales()
    {
        return $this->hasMany(Penjualan::class, 'perusahaan_cabang_id');
    }
}
