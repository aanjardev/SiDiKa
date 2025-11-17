<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    protected $fillable = [
        'kode_sku',
        'id_kategori',
        'nama_produk',
        'harga_jual',
        'stok_produk',
        'deskripsi_produk',
        'status',
        'grade',
        'harga_beli',
        'harga_servis',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id');
    }

    public function gambar()
    {
        return $this->hasMany(GambarProduk::class, 'id_produk', 'id');
    }

    public function gambarUtama()
    {
        return $this->hasOne(GambarProduk::class, 'id_produk', 'id')->where('is_main', true);
    }


    public function semuaGambar()
    {
        return $this->hasMany(GambarProduk::class, 'id_produk', 'id');
    }

}
