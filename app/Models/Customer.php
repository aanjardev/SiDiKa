<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $table = 'customer';

     protected $fillable = [
        'kode_customer', // TAMBAH INI
        'nama',
        'no_telp',
        'identitas',
        'jenis_kelamin',
        'alamat',
        'referensi',
        'keterangan',
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            // Prefix: CU (Customer)
            $prefix = 'CU';

            $latestCode = static::where('kode_customer', 'like', $prefix . '%')
                                ->latest('kode_customer')
                                ->pluck('kode_customer')
                                ->first();

            $number = 1;

            if ($latestCode) {
                // Ambil angka dari kode terakhir
                $number = (int) substr($latestCode, -4);
                $number++;
            }

            // Format kode customer: CU[000X]
            $customer->kode_customer = $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
        });
    }

}
