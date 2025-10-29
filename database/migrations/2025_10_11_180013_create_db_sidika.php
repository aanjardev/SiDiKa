<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat semua tabel yang dibutuhkan SiDiKa, termasuk yang diminta (users, kategori, detail_produk).
     */
    public function up(): void
    {
        // =================================================================
        // 1. DATA MASTER & AKSES (users, kategori, perusahaan)
        // =================================================================

        // 1.1 Tabel users (MODIFIKASI) - Asumsi sudah ada dari migrasi default Laravel
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom 'role' untuk kebutuhan RBAC
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['manager', 'operasional'])->default('operasional')->after('password');
            }
        });

        // 1.2 Tabel Kategori (BARU)
        Schema::create('kategori', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori', 50);
            $table->timestamps();
        });

        // 1.3 Tabel Perusahaan/Cabang
        Schema::create('perusahaan_cabang', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50);
            $table->string('alamat', 100);
            $table->string('nomor_telepon', 20);
            $table->timestamps();
        });

        // 1.4 Tabel Customer (CRM)
        Schema::create('customer', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50);
            $table->string('identitas', 20)->nullable();
            $table->string('no_telp', 20);
            $table->string('alamat', 100)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('referensi', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 1.5 Tabel Karyawan (SDM)
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_lengkap', 50);
            $table->string('nik', 20)->unique();
            $table->string('jabatan', 50);
            $table->integer('gaji')->nullable(); // Gaji saat ini
            $table->date('tanggal_masuk');
            $table->date('tanggal_keluar')->nullable();
            $table->enum('status', ['aktif', 'non-aktif'])->default('aktif');
            $table->string('nomor_telepon', 15);
            $table->string('alamat', 100);
            $table->timestamps();
        });

        // =================================================================
        // 2. PRODUK, INVENTARIS & QC
        // =================================================================

        // 2.1 Tabel Kondisi (QC Parameter)
        Schema::create('kondisi', function (Blueprint $table) {
            $table->id();
            $table->string('fisik', 100)->nullable();
            $table->string('baut', 50)->nullable();
            $table->string('tutup_usb', 50)->nullable();
            $table->string('grip', 50)->nullable();
            $table->string('jamur_lensa', 100)->nullable();
            $table->string('view_finder', 50)->nullable();
            $table->string('mounting', 50)->nullable();
            $table->string('slot_memori', 50)->nullable();
            $table->string('jamur_sensor', 100)->nullable();
            $table->string('lcd', 100)->nullable();
            $table->string('tombol', 50)->nullable();
            $table->string('zoom_lensa', 50)->nullable();
            $table->string('af_lensa', 50)->nullable();
            $table->string('diafragma_lensa', 50)->nullable();
            $table->string('kalibrasi_fokus', 50)->nullable();
            $table->string('flash', 100)->nullable();
            $table->string('sound_mic', 50)->nullable();
            $table->string('lain_lain', 255)->nullable();
            $table->timestamps();
        });

        // 2.2 Tabel Produk (Master Data)
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->string('kode_sku', 20)->unique();
            $table->foreignId('kategori_id')->constrained('kategori')->onDelete('cascade');
            $table->string('nama_produk', 200);
            $table->integer('harga_jual')->nullable();
            $table->integer('harga_beli')->nullable();
            $table->integer('harga_servis')->nullable();
            $table->timestamps();
        });

        // 2.3 Tabel Detail Produk (INVENTARIS - DIKEMBALIKAN)
        Schema::create('detail_produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk')->onDelete('cascade');
            $table->foreignId('kondisi_id')->constrained('kondisi')->onDelete('restrict');
            $table->string('serial_number', 30)->unique();
            $table->integer('stok_produk')->default(0); // Stok per serial number (1 unit)
            $table->text('deskripsi_produk')->nullable();
            $table->enum('status', ['Second', 'Baru'])->default('Second');
            $table->enum('grade', ['Unggulan', 'Standar', 'Minus'])->default('Standar');
            $table->string('kelengkapan', 255)->nullable();
            $table->timestamps();
        });

        // 2.4 Tabel Gambar Produk Detail (Revisi Multi-Gambar)
        Schema::create('gambar_produk_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk')->onDelete('cascade');
            $table->string('path_gambar');
            $table->integer('urutan')->nullable();
            $table->timestamps();
        });

        // =================================================================
        // 3. TRANSAKSI
        // =================================================================

        // 3.1 Tabel Pembelian (Master Transaksi Beli dari Customer)
        Schema::create('pembelian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customer')->onDelete('restrict');
            $table->foreignId('perusahaan_cabang_id')->constrained('perusahaan_cabang')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->date('tanggal');
            $table->enum('kas', ['transfer', 'cash']);
            $table->timestamps();
        });

        // 3.2 Tabel Detail Pembelian
        Schema::create('detail_pembelian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembelian_id')->constrained('pembelian')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produk')->onDelete('restrict');
            // Kondisi Awal Pembelian sudah tercatat di tabel kondisi melalui detail_produk
            $table->string('serial_number', 30)->nullable();
            $table->integer('qty')->default(1);
            $table->integer('harga_tawaran')->nullable();
            $table->integer('harga_terakhir')->nullable();
            $table->enum('status_pembelian', ['deal', 'tidak'])->default('tidak');
            $table->timestamps();
        });

        // 3.3 Tabel Penjualan (Master Transaksi Jual)
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customer')->onDelete('restrict');
            $table->foreignId('perusahaan_cabang_id')->constrained('perusahaan_cabang')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->integer('harga_total');
            $table->double('diskon')->nullable();
            $table->enum('kas', ['transfer', 'cash']);
            $table->string('keterangan', 200)->nullable();
            $table->date('tanggal');
            $table->timestamps();
        });

        // 3.4 Tabel Detail Penjualan
        Schema::create('detail_penjualan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penjualan_id')->constrained('penjualan')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produk')->onDelete('restrict');
            $table->string('serial_number', 30)->nullable();
            $table->integer('qty');
            $table->integer('harga_jual_satuan'); // REVISI KRITIS: Harga jual saat transaksi
            $table->double('harga_depresiasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_penjualan');
        Schema::dropIfExists('penjualan');
        Schema::dropIfExists('detail_pembelian');
        Schema::dropIfExists('pembelian');
        Schema::dropIfExists('gambar_produk_detail');
        Schema::dropIfExists('detail_produk');
        Schema::dropIfExists('produk');
        Schema::dropIfExists('kondisi');
        Schema::dropIfExists('karyawan');
        Schema::dropIfExists('customer');
        Schema::dropIfExists('perusahaan_cabang');
        Schema::dropIfExists('kategori');

        // Hapus kolom 'role' dari tabel users
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
