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

    protected static function booted(): void
    {
        static::deleting(function (GambarProduk $gambar) {
            if ($gambar->path_gambar) {
                Storage::disk('r2')->delete($gambar->path_gambar);
            }
        });
    }

    public function getUrlAttribute(): string
    {
        $base = rtrim(env('CDN_BASE_URL', 'https://sidika.qurrotul-ainii0266.workers.dev'), '/');

        if ($base) {
            return $base . '/' . ltrim($this->path_gambar, '/');
        }

        return Storage::disk('r2')->url($this->path_gambar);
    }
}
