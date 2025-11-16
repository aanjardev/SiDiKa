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
        Schema::create('catalog_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->default('Dinoyo Kamera');
            $table->string('logo_path')->nullable();
            $table->string('banner_path')->nullable();
            $table->string('brand_logo_1_path')->nullable();
            $table->string('brand_logo_2_path')->nullable();
            $table->string('brand_logo_3_path')->nullable();
            $table->string('brand_logo_4_path')->nullable();
            $table->string('social_facebook')->nullable();
            $table->string('social_instagram')->nullable();
            $table->string('social_whatsapp')->nullable();
            $table->string('social_tiktok')->nullable();
            $table->string('social_youtube')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->text('address_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalog_settings');
    }
};

