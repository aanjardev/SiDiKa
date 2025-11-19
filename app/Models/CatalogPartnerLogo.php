<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogPartnerLogo extends Model
{
    use HasFactory;
    protected $table = 'catalog_partner_logos';
    protected $fillable = [
        'logo_path',
        'catalog_setting_id'
    ];
    
}
