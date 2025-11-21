<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualan';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function perusahaan_cabang()
    {
        return $this->belongsTo(Branch::class, 'perusahaan_cabang_id');
    }

    public function branch()
    {
        return $this->perusahaan_cabang();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detail_penjualan()
    {
        return $this->hasMany(DetailPenjualan::class, 'penjualan_id');
    }

    public function details()
    {
        return $this->detail_penjualan();
    }
}
