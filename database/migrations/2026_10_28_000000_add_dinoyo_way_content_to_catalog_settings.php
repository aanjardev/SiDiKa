<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catalog_settings', function (Blueprint $table) {
            $table->json('dinoyo_way_content')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('catalog_settings', function (Blueprint $table) {
            $table->dropColumn('dinoyo_way_content');
        });
    }
};
