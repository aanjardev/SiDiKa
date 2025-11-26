<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('perusahaan_cabang')->truncate();

        Schema::enableForeignKeyConstraints();

        Branch::create([
            'nama' => 'Dinoyo Kamera 1',
            'alamat' => 'Jl. MT Haryono No. 123, Dinoyo, Malang',
            'nomor_telepon' => '081234567891',
            'jam_buka' => '08:30:00',
            'jam_tutup' => '17:00:00',
            'link_maps' => 'https://maps.google.com/?q=-7.966620,112.632632'
        ]);

        Branch::create([
            'nama' => 'Dinoyo Kamera 2',
            'alamat' => 'Jl. Gajayana No. 45, Dinoyo, Malang',
            'nomor_telepon' => '081234567892',
            'jam_buka' => '08:00:00',
            'jam_tutup' => '17:00:00',
            'link_maps' => 'https://maps.google.com/?q=-7.970000,112.630000'
        ]);

        Branch::create([
            'nama' => 'Dinoyo Kamera 3',
            'alamat' => 'Jl. Raya Tlogomas No. 67, Malang',
            'nomor_telepon' => '081234567893',
            'jam_buka' => '08:30:00',
            'jam_tutup' => '17:30:00',
            'link_maps' => 'https://maps.google.com/?q=-7.955000,112.640000'
        ]);
    }
}
