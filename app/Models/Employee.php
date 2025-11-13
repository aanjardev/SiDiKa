<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $fillable = [
        'nama_lengkap',
        'nik',
        'jabatan',
        'gaji',
        'tanggal_masuk',
        'tanggal_keluar',
        'status',
        'nomor_telepon',
        'alamat'
    ];
}
