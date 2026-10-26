<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/** Local catalog fixtures; run explicitly, never as part of DatabaseSeeder. */
class CatalogPaginationDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (!app()->environment('local') || !in_array(DB::connection()->getConfig('host'), ['localhost', '127.0.0.1', '::1'], true)) {
            throw new RuntimeException('Data DEMO-KATALOG hanya boleh dibuat pada database lokal.');
        }

        $categories = DB::table('kategori')->orderBy('id')->pluck('id');
        if ($categories->isEmpty()) {
            throw new RuntimeException('Kategori produk harus tersedia terlebih dahulu.');
        }

        $names = [
            'Canon EOS R6 Mark II dengan Lensa RF 24-105mm dan Paket Aksesori Fotografi Profesional',
            'Sony Alpha A7 IV dengan Lensa FE 24-70mm F2.8 dan Dua Baterai Cadangan',
            'Nikon Z6 II Body dan Lensa Nikkor Z 24-120mm F4 S',
            'Fujifilm X-T5 dengan Lensa XF 16-55mm F2.8 R LM WR',
            'Tripod Video Profesional dengan Fluid Head, Tas Penyimpanan, Quick Release Plate dan Extension Arm',
            'Microphone Wireless Dual Channel untuk Kamera Mirrorless dan Perangkat Mobile',
            'Lensa Sigma 35mm F1.4 DG DN Art',
            'Flash Godox V1 dan Wireless Trigger',
        ];

        DB::transaction(function () use ($categories, $names) {
            for ($i = 1; $i <= 320; $i++) {
                $sku = sprintf('DEMO-KAT-%04d', $i);
                if (DB::table('produk')->where('kode_sku', $sku)->exists()) {
                    continue;
                }
                DB::table('produk')->insert([
                    'kode_sku' => $sku,
                    'id_kategori' => $categories[($i - 1) % $categories->count()],
                    'nama_produk' => sprintf('DEMO-KATALOG %03d - %s', $i, $names[($i - 1) % count($names)]),
                    'harga_jual' => 750000 + ($i % 80) * 375000,
                    'harga_beli' => 500000 + ($i % 80) * 300000,
                    'harga_servis' => 0,
                    'stok_produk' => $i % 11 === 0 ? 0 : 1 + ($i % 10),
                    'status' => $i % 3 === 0 ? 'Baru' : 'Second',
                    'grade' => $i % 4 === 0 ? 'Unggulan' : 'Standar',
                    'is_visible' => true, 'is_archived' => false,
                    'deskripsi_produk' => '[DEMO-KATALOG] Produk simulasi lokal untuk menguji pagination, pencarian, kategori dan urutan harga. Bukan inventaris asli.',
                    'created_at' => now()->subSeconds($i),
                    'updated_at' => now()->subSeconds($i),
                ]);
            }
        });

        $demo = DB::table('produk')->where('kode_sku', 'like', 'DEMO-KAT-%')->count();
        $visible = DB::table('produk')->where('is_visible', true)->where('is_archived', false)->count();
        $this->command?->info("{$demo} produk DEMO-KATALOG; total {$visible} produk katalog (" . (int) ceil($visible / 16) . ' halaman).');
    }
}
