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
        // Schema::create('produk', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('kode_sku', 20);
        //     $table->unsignedBigInteger('id_kategori');
        //     $table->string('nama_produk', 200);
        //     $table->integer('harga_jual')->nullable();
        //     $table->integer('stok_produk');
        //     $table->text('deskripsi_produk')->nullable();
        //     $table->enum('status', ['Second', 'Baru']);
        //     $table->enum('grade', ['Unggulan', 'Standar', 'Minus'])->default('Standar');
        //     $table->timestamps();

        //     $table->foreign('id_kategori')->references('id')->on('kategori')->onDelete('cascade');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('produk');
    }
};
