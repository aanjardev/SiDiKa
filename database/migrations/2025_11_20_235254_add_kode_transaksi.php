<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tambah kolom kode transaksi ke tabel pembelian
        Schema::table('pembelian', function (Blueprint $table) {
            // Kita gunakan 'string' dan memastikan kode_transaksi ini unik dan tidak boleh kosong
            $table->string('kode_transaksi', 20)->unique()->after('id');
        });

        // Tambah kolom kode transaksi ke tabel penjualan
        Schema::table('penjualan', function (Blueprint $table) {
            $table->string('kode_transaksi', 20)->unique()->after('id');
        });

        // Tambah kolom kode entitas ke tabel customer
        Schema::table('customer', function (Blueprint $table) {
            $table->string('kode_customer', 10)->unique()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback untuk pembelian
        Schema::table('pembelian', function (Blueprint $table) {
            $table->dropColumn('kode_transaksi');
        });

        // Rollback untuk penjualan
        Schema::table('penjualan', function (Blueprint $table) {
            $table->dropColumn('kode_transaksi');
        });

        // Rollback untuk customer
        Schema::table('customer', function (Blueprint $table) {
            $table->dropColumn('kode_customer');
        });
    }
};
