<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $table = 'customer';



    protected $fillable = [
        'nama',
        'identitas',
        'no_telp',
        'alamat',
        'jenis_kelamin',
        'referensi',
        'keterangan',
    ];
}
