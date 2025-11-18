<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogBanners extends Model
{
    use HasFactory;
    protected $table = 'catalog_banners';
    protected $fillable = [
        'banner_path',
        'catalog_setting_id'
    ];
}
