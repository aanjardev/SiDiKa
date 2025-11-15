<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use App\Models\Branch as PerusahaanCabang;
use App\Models\User;
use App\Models\ItemPembelian as ItemPembelianDraft;

class Pembelian extends Model
{
    use HasFactory;
    protected $table = 'pembelian';

    // Izinkan 'created_at' diformat sebagai objek Carbon (Tanggal)
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke Customer (Satu Pembelian dimiliki oleh satu Customer)
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Relasi ke Cabang (Satu Pembelian milik satu Cabang)
     * Ganti 'PerusahaanCabang' dengan 'Branch' jika itu nama model Anda
     */
    public function perusahaan_cabang()
    {
        return $this->belongsTo(PerusahaanCabang::class, 'perusahaan_cabang_id');
    }

    /**
     * Relasi ke User/Karyawan (Satu Pembelian diinput oleh satu User)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Item Draft (Satu Pembelian memiliki BANYAK item draft)
     */
    public function item_pembelian_draft()
    {
        return $this->hasMany(ItemPembelianDraft::class, 'pembelian_id');
    }
}
