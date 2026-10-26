<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/** Explicitly invoked local fixtures; intentionally excluded from DatabaseSeeder. */
class PaginationDemoSeeder extends Seeder
{
    public function run(): void
    {
        $connection = DB::connection();
        if (!app()->environment('local') || !in_array($connection->getConfig('host'), ['localhost', '127.0.0.1', '::1'], true)) {
            throw new RuntimeException('Data DEMO-PAG hanya boleh dibuat pada database lokal.');
        }

        $branchIds = DB::table('perusahaan_cabang')->where('is_active', 1)->orderBy('id')->pluck('id');
        $userId = DB::table('users')->where('status', 'active')->orderBy('id')->value('id');
        $categoryId = DB::table('kategori')->orderBy('id')->value('id');
        if ($branchIds->isEmpty() || !$userId || !$categoryId) {
            throw new RuntimeException('Cabang aktif, user aktif, dan kategori harus tersedia terlebih dahulu.');
        }

        // Query builder avoids normal transaction hooks and changes no existing stock.
        DB::transaction(function () use ($branchIds, $userId, $categoryId) {
            $customers = [];
            for ($i = 1; $i <= 12; $i++) {
                $code = sprintf('DPAG%04d', $i);
                $customers[] = DB::table('customer')->where('kode_customer', $code)->value('id')
                    ?? DB::table('customer')->insertGetId([
                        'kode_customer' => $code,
                        'nama' => sprintf('DEMO PAG - Fotografi Nusantara Cabang Malang %02d', $i),
                        'no_telp' => sprintf('08000000%04d', $i),
                        'jenis_kelamin' => $i % 2 ? 'L' : 'P',
                        'alamat' => 'Data simulasi lokal untuk pengujian pagination',
                        'keterangan' => '[DEMO-PAG] Customer uji pagination, bukan data asli.',
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
            }

            $names = [
                'DEMO PAG - Canon EOS R6 Mark II Body dengan Lensa RF 24-105mm, Battery Grip, Dua Baterai Cadangan, Tas Kamera dan Aksesori Fotografi Profesional',
                'DEMO PAG - Sony Alpha A7 IV dengan Lensa FE 24-70mm F2.8, Tripod, Microphone Wireless dan Paket Kelengkapan Video Dokumentasi',
                'DEMO PAG - Nikon Z6 II Body dan Lensa Nikkor Z 24-120mm F4 S',
                'DEMO PAG - Fujifilm X-T5 dengan Lensa XF 16-55mm F2.8 R LM WR',
                'DEMO-PAG-' . str_repeat('NamaProdukTanpaSpasi', 8),
            ];
            $products = [];
            foreach ($names as $index => $name) {
                $sku = sprintf('DEMO-PAG-PR-%02d', $index + 1);
                $products[] = DB::table('produk')->where('kode_sku', $sku)->value('id')
                    ?? DB::table('produk')->insertGetId([
                        'kode_sku' => $sku, 'id_kategori' => $categoryId,
                        'nama_produk' => $name, 'harga_jual' => 5000000 + $index * 1000000,
                        'harga_beli' => 4000000 + $index * 1000000,
                        'stok_produk' => 0, 'status' => 'Second', 'grade' => 'Standar',
                        'is_visible' => false, 'is_archived' => true,
                        'deskripsi_produk' => '[DEMO-PAG] Produk simulasi; disembunyikan dari katalog publik.',
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
            }

            for ($i = 1; $i <= 200; $i++) {
                $time = now()->subMinutes($i);
                $customerId = $customers[($i - 1) % count($customers)];
                $branchId = $branchIds[($i - 1) % $branchIds->count()];
                $amount = 5000000 + ($i % 40) * 250000;
                $common = [
                    'customer_id' => $customerId, 'perusahaan_cabang_id' => $branchId,
                    'user_id' => $userId, 'created_at' => $time, 'updated_at' => $time,
                    'keterangan' => '[DEMO-PAG] Data simulasi lokal untuk menguji pagination dan teks tabel.',
                ];

                $purchaseCode = sprintf('DEMO-PAG-PB-%04d', $i);
                if (!DB::table('pembelian')->where('kode_transaksi', $purchaseCode)->exists()) {
                    $status = ['deal', 'draft', 'tidak_deal'][($i - 1) % 3];
                    $id = DB::table('pembelian')->insertGetId($common + [
                        'kode_transaksi' => $purchaseCode, 'status_pembelian' => $status,
                        'harga_tawaran_customer' => $amount + 500000,
                        'harga_tawaran_toko' => $amount,
                        'harga_deal' => $status === 'deal' ? $amount : null,
                    ]);
                    for ($item = 0; $item < 1 + ($i % 3); $item++) {
                        DB::table('item_pembelian_draft')->insert([
                            'pembelian_id' => $id, 'nama_item' => $names[($i + $item) % count($names)],
                            'kategori_id' => $categoryId, 'qty' => 1,
                            'status' => 'Second', 'status_qc' => 'diarsipkan',
                            'harga_beli' => $amount, 'grade' => 'Standar',
                            'catatan_qc' => '[DEMO-PAG] Item simulasi pagination.',
                            'created_at' => $time, 'updated_at' => $time,
                        ]);
                    }
                }

                $saleCode = sprintf('DEMO-PAG-PJ-%04d', $i);
                if (!DB::table('penjualan')->where('kode_transaksi', $saleCode)->exists()) {
                    $itemCount = 1 + ($i % 3);
                    $id = DB::table('penjualan')->insertGetId($common + [
                        'kode_transaksi' => $saleCode, 'harga_total' => $amount * $itemCount,
                        'diskon' => 0, 'kas' => $i % 2 ? 'cash' : 'transfer',
                        'tanggal' => $time->toDateString(),
                    ]);
                    for ($item = 0; $item < $itemCount; $item++) {
                        DB::table('detail_penjualan')->insert([
                            'penjualan_id' => $id, 'produk_id' => $products[($i + $item) % count($products)],
                            'qty' => 1, 'harga_jual_satuan' => $amount, 'harga_depresiasi' => 0,
                            'created_at' => $time, 'updated_at' => $time,
                        ]);
                    }
                }
            }
        });

        foreach (['pembelian', 'penjualan'] as $table) {
            $count = DB::table($table)->where('kode_transaksi', 'like', 'DEMO-PAG-%')->count();
            $total = DB::table($table)->count();
            $this->command?->info("{$table}: {$count} data DEMO-PAG; total {$total} data (" . (int) ceil($total / 10) . ' halaman).');
        }
    }
}
