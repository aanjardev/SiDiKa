<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kategori')->insert([
            ['nama_kategori' => 'Kamera DSLR'],
            ['nama_kategori' => 'Kamera Mirrorless'],
            ['nama_kategori' => 'Kamera Digital'],
            ['nama_kategori' => 'Handycam'],
            ['nama_kategori' => 'Kamera Instan'],
            ['nama_kategori' => 'Kamera Lain'],
            ['nama_kategori' => 'Lensa'],
            ['nama_kategori' => 'Baterai/Charger'],
            ['nama_kategori' => 'Kartu Memori'],
            ['nama_kategori' => 'Aksesoris Lain'],
        ]);
    }
}
