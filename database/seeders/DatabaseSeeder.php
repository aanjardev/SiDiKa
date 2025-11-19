<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Menggabungkan Seeder Admin, Kategori, dan Produk.
     */
    public function run(): void
    {

        $this->call([
            AdminSeeder::class,
            KategoriSeeder::class,
            ProdukSeeder::class,
            BranchSeeder::class,
            KaryawanSeeder::class,
            CatalogSeeder::class,
        ]);
        // Nonaktifkan foreign key checks untuk operasi TRUNCATE
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // // TRUNCATE (Hapus data lama agar bersih saat seeding)
        // DB::table('gambar_produk_detail')->truncate();
        // DB::table('produk')->truncate();
        // DB::table('kategori')->truncate();
        // // Hanya truncate users jika Anda yakin ingin menghapus semua user lama, atau gunakan update
        // // DB::table('users')->truncate();

        // // =================================================================
        // // 1. SEEDING USERS & KATEGORI (Data Master Utama)
        // // =================================================================

        // // 1.1 AdminSeeder Logic (Memodifikasi user_id yang sudah ada, atau membuat jika tidak ada)
        // // Jika tabel users sudah ada, ini hanya menambahkan role pada admin default.
        // DB::table('users')->updateOrInsert(
        //     ['username' => 'admin'], // Kunci unik untuk pencarian
        //     [
        //         'name' => 'Admin SiDiKa', // Sesuai kolom name yang Anda tambahkan
        //         'email' => 'admin@sidika.com', // Kolom email
        //         'password' => Hash::make('admin123'),
        //         'role' => 'admin', // Kolom role kustom
        //         'remember_token' => Str::random(10), // Opsional
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ]
        // );

        // // 1.2 KategoriSeeder Logic
        // $kategoriData = [
        //     ['nama_kategori' => 'Kamera DSLR'],
        //     ['nama_kategori' => 'Kamera Mirrorless'],
        //     ['nama_kategori' => 'Kamera Digital'],
        //     ['nama_kategori' => 'Handycam'],
        //     ['nama_kategori' => 'Kamera Instan'],
        //     ['nama_kategori' => 'Kamera Lain'],
        //     ['nama_kategori' => 'Lensa'],
        //     ['nama_kategori' => 'Baterai/Charger'],
        //     ['nama_kategori' => 'Kartu Memori'],
        //     ['nama_kategori' => 'Aksesoris Lain'],
        // ];

        // DB::table('kategori')->insert($kategoriData);

        // // Ambil ID Kategori yang baru dibuat untuk digunakan di ProdukSeeder
        // $kategoriMap = DB::table('kategori')->pluck('id', 'nama_kategori');

        // // =================================================================
        // // 2. SEEDING PRODUK (Membuat Produk dan Gambar)
        // // =================================================================

        // // Array Produk yang akan di-looping
        // $listProduk = [
        //     // Produk 1: Handycam
        //     [
        //         'kode_sku' => '1274OBOOPL', 'nama_produk' => 'Handycam Panasonic SDR S26 Second Kondisi Baik',
        //         'kategori_key' => 'Handycam', 'harga_jual' => 900000, 'stok_produk' => 1,
        //         'status' => 'Second', 'grade' => 'Unggulan', 'deskripsi_produk' => 'KONDISI: Layar Flip 180°, Fungsional Normal. KELENGKAPAN: Kamera, Lensa, Baterai, Charger, Nota Pembelian Dinoyo Kamera',
        //         'gambar_list' => [
        //             ['path' => 'gambar_produk/11.png', 'urutan' => 1], ['path' => 'gambar_produk/12.jpg', 'urutan' => 2],
        //             ['path' => 'gambar_produk/13.jpg', 'urutan' => 3], ['path' => 'gambar_produk/14.jpg', 'urutan' => 4],
        //         ]
        //     ],
        //     // Produk 2: Nikon D5200
        //     [
        //         'kode_sku' => '1267OHOOVT', 'nama_produk' => 'Nikon D5200 + kit 18 55mm Kondisi Baik',
        //         'kategori_key' => 'Kamera DSLR', 'harga_jual' => 2450000, 'stok_produk' => 1,
        //         'status' => 'Second', 'grade' => 'Standar', 'deskripsi_produk' => 'TYPE : Nikon D5200 + kit 18 55mm. KONDISI: Layar Flip, LCD Vignet, Fungsional Normal. KELENGKAPAN: Kamera, Lensa, Baterai, Charger, Nota Pembelian Dinoyo Kamera',
        //         'gambar_list' => [
        //             ['path' => 'gambar_produk/21.png', 'urutan' => 1], ['path' => 'gambar_produk/22.jpg', 'urutan' => 2],
        //             ['path' => 'gambar_produk/23.jpg', 'urutan' => 3], ['path' => 'gambar_produk/24.jpg', 'urutan' => 4],
        //             ['path' => 'gambar_produk/25.jpg', 'urutan' => 5], ['path' => 'gambar_produk/26.png', 'urutan' => 6],
        //         ]
        //     ],
        //     // Produk 3: Fujifilm X a10
        //     [
        //         'kode_sku' => '1271AEOOWS', 'nama_produk' => 'Fujifilm X a10 + kit 16 50mm (Pink) Kondisi Baik',
        //         'kategori_key' => 'Kamera Mirrorless', 'harga_jual' => 3300000, 'stok_produk' => 1,
        //         'status' => 'Second', 'grade' => 'Unggulan', 'deskripsi_produk' => 'TYPE : Fujifilm X a10. KONDISI: Wifi Berfungsi, Layar Flip 180°, LCD ada whitespot tipis. KELENGKAPAN: Kamera, Lensa, Baterai, Charger, Nota Pembelian Dinoyo Kamera',
        //         'gambar_list' => [
        //             ['path' => 'gambar_produk/31.png', 'urutan' => 1], ['path' => 'gambar_produk/32.jpg', 'urutan' => 2],
        //             ['path' => 'gambar_produk/33.jpg', 'urutan' => 3], ['path' => 'gambar_produk/34.jpg', 'urutan' => 4],
        //             ['path' => 'gambar_produk/35.jpg', 'urutan' => 5], ['path' => 'gambar_produk/36.png', 'urutan' => 6],
        //         ]
        //     ],
        //     // Produk 4: Lensa Nikon 28 300mm
        //     [
        //         'kode_sku' => '1258OOOOJT', 'nama_produk' => 'Lensa Nikon 28 300mm VR Kondisi Baik',
        //         'kategori_key' => 'Lensa', 'harga_jual' => 5200000, 'stok_produk' => 1,
        //         'status' => 'Second', 'grade' => 'Unggulan', 'deskripsi_produk' => 'KONDISI: Body Fisik Paint Lost, Jamur Tipis/Debu Micro. KELENGKAPAN: Lensa, Tutup Depan, Tutup Belakang, Nota Pembelian Dinoyo Kamera',
        //         'gambar_list' => [
        //             ['path' => 'gambar_produk/41.png', 'urutan' => 1], ['path' => 'gambar_produk/42.jpg', 'urutan' => 2],
        //             ['path' => 'gambar_produk/43.jpg', 'urutan' => 3], ['path' => 'gambar_produk/44.png', 'urutan' => 4],
        //         ]
        //     ],
        //     // Produk 5, 8, 9, 10, 14, 15, 16, 17, 19, 17 (Sisanya dari list Anda)
        //     [
        //         'kode_sku' => '1065', 'nama_produk' => 'Instax mini Li Play Brown Second Kondisi Baik',
        //         'kategori_key' => 'Kamera Instan', 'harga_jual' => 1900000, 'stok_produk' => 1,
        //         'status' => 'Second', 'grade' => 'Unggulan', 'deskripsi_produk' => 'KONDISI: Fungsional Normal, Body Paintlos. KELENGKAPAN: Kamera, Baterai, Nota Pembelian Dinoyo Kamera',
        //         'gambar_list' => [['path' => 'gambar_produk/51.png', 'urutan' => 1], ['path' => 'gambar_produk/52.jpg', 'urutan' => 2], ['path' => 'gambar_produk/53.jpg', 'urutan' => 3]]
        //     ],
        //      [
        //         'kode_sku' => '1225OEOOPL', 'nama_produk' => 'Canon Ixus 185 Kondisi Baik',
        //         'kategori_key' => 'Kamera Digital', 'harga_jual' => 2350000, 'stok_produk' => 1,
        //         'status' => 'Second', 'grade' => 'Unggulan', 'deskripsi_produk' => 'KONDISI: Fungsional Normal, LCD Baik. KELENGKAPAN: Kamera, Baterai, Charger, Nota Pembelian Dinoyo Kamera',
        //         'gambar_list' => [['path' => 'gambar_produk/61.png', 'urutan' => 1], ['path' => 'gambar_produk/63.jpg', 'urutan' => 2], ['path' => 'gambar_produk/66.png', 'urutan' => 3]]
        //     ],
        //     [
        //         'kode_sku' => '1052COOOPL', 'nama_produk' => 'Insta 360 One RS Twin (4k + 360) Kondisi Baik',
        //         'kategori_key' => 'Kamera Lain', 'harga_jual' => 5250000, 'stok_produk' => 1,
        //         'status' => 'Second', 'grade' => 'Unggulan', 'deskripsi_produk' => 'KONDISI: Kamera Aksi Modular & Sistem Kamera 360, Touch Screen Baik. KELENGKAPAN: Box, Kamera 4k dan 360, Baterai, Nota Pembelian Dinoyo Kamera',
        //         'gambar_list' => [['path' => 'gambar_produk/71.png', 'urutan' => 1], ['path' => 'gambar_produk/72.png', 'urutan' => 2], ['path' => 'gambar_produk/75.jpg', 'urutan' => 3]]
        //     ],
        //     [
        //         'kode_sku' => 'BAT001', 'nama_produk' => 'Baterai Kamera Canon LP-E6',
        //         'kategori_key' => 'Baterai/Charger', 'harga_jual' => 250000, 'stok_produk' => 10,
        //         'status' => 'Baru', 'grade' => 'Standar', 'deskripsi_produk' => 'Kompatibel dengan Kamera Canon R / 6D / 6DII / 7D II / 7D / 70D / 80D',
        //         'gambar_list' => [['path' => 'gambar_produk/81.png', 'urutan' => 1]]
        //     ],
        //     [
        //         'kode_sku' => 'MEM001', 'nama_produk' => 'Memory Card Sandisk 16GB Ultra SDHC',
        //         'kategori_key' => 'Kartu Memori', 'harga_jual' => 150000, 'stok_produk' => 10,
        //         'status' => 'Baru', 'grade' => 'Standar', 'deskripsi_produk' => 'Memory Card Sandisk 16GB Ultra SDHC',
        //         'gambar_list' => [['path' => 'gambar_produk/91.png', 'urutan' => 1]]
        //     ],
        //     [
        //         'kode_sku' => 'ACS001', 'nama_produk' => 'Tali Strap Leher Kamera Motif Batik',
        //         'kategori_key' => 'Aksesoris Lain', 'harga_jual' => 120000, 'stok_produk' => 10,
        //         'status' => 'Baru', 'grade' => 'Standar', 'deskripsi_produk' => '-',
        //         'gambar_list' => [['path' => 'gambar_produk/101.png', 'urutan' => 1]]
        //     ],
        // ];

        // // 3. LOOPING UNTUK MEMASUKKAN DATA PRODUK DAN GAMBAR
        // foreach ($listProduk as $data) {

        //     // 3.1 Insert Produk (Tabel Produk)
        //     $produkId = DB::table('produk')->insertGetId([
        //         'kode_sku' => $data['kode_sku'],
        //         'kategori_id' => $kategoriMap[$data['kategori_key']],
        //         'nama_produk' => $data['nama_produk'],
        //         'harga_jual' => $data['harga_jual'],
        //         'stok_produk' => $data['stok_produk'], // Stok disimpan di tabel produk
        //         'status' => $data['status'],
        //         'grade' => $data['grade'] ?? 'Standar',
        //         'deskripsi_produk' => $data['deskripsi_produk'],
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ]);

        //     // 3.2 Insert Gambar (Tabel Gambar_Produk_Detail)
        //     foreach ($data['gambar_list'] as $gambar) {
        //         DB::table('gambar_produk_detail')->insert([
        //             'produk_id' => $produkId,
        //             'path_gambar' => $gambar['path'],
        //             'urutan' => $gambar['urutan'],
        //             'created_at' => now(),
        //             'updated_at' => now(),
        //         ]);
        //     }

        //     // Catatan: Karena kita menyederhanakan, kita TIDAK menginsert ke tabel 'kondisi' dan 'detail_produk' secara terpisah di seeder,
        //     // melainkan menggabungkan data inventaris ke tabel 'produk'.
        //     // Untuk produk yang tidak memiliki SN unik (aksesoris), ini lebih sederhana.
        // }

        // // Aktifkan kembali foreign key checks
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
