<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CatalogSettings extends Model
{
    use HasFactory;
    protected $table = 'catalog_settings';
    protected $fillable = [
        'nama_website',
        'logo_path',
        'nomor_telfon',
        'description',
        'facebook_link',
        'instagram_link',
        'tiktok_link',
        'youtube_link',
        'tokopedia_link',
        'shopee_link',
    ];
    public function getLogoUrlAttribute()
    {
        return $this->logo_path
            ? Storage::disk('r2')->url($this->logo_path)
            : null;
    }
    protected $appends = ['logo_url'];

}
