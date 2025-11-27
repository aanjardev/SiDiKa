<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;
    protected $table = 'kategori';
    protected $fillable = [
        'nama_kategori',
    ];

    /**
     * Relasi dengan Produk
     */
    public function produk()
    {
        return $this->hasMany(Produk::class, 'id_kategori', 'id');
    }

    /**
     * Cek apakah kategori digunakan oleh produk
     */
    public function isUsed()
    {
        return $this->produk()->exists();
    }
}
