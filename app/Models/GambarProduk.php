<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GambarProduk extends Model
{
    use HasFactory;

    protected $table = 'gambar_produk';

    protected $fillable = ['id_produk', 'path_gambar', 'is_main'];

    protected $appends = ['url'];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id');
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('r2')->url($this->path_gambar);
    }
}
