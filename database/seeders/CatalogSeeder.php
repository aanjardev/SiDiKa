<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        // 1. catalog_settings
        $catalogId = DB::table('catalog_settings')->insertGetId([
            'nama_website' => 'Dinoyo Kamera',  
            'nomor_telfon' => '082345670014',
            'description' => 'Toko kamera terpercaya di Malang dan Pasuruan. Menyediakan berbagai kebutuhan fotografi dan videografi dengan kualitas terjamin.',
            'logo_path' => 'mainIMG/logoDinoyo.png',
            'facebook_link' => 'https://www.facebook.com/jualkameramalang',
            'youtube_link' => 'https://www.youtube.com/@DINOYOKAMERA_OFFICIAL',
            'instagram_link' => 'https://www.instagram.com/dinoyokamera/',
            'tiktok_link' => 'https://www.tiktok.com/@dinoyokamera',
            'tokopedia_link' => 'https://www.tokopedia.com/dinoyo-kamera',
            'shopee_link' => 'https://shopee.co.id/dinoyokamera',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. catalog_banners
        DB::table('catalog_banners')->insert([
            [
                'catalog_setting_id' => 1,
                'banner_path' => 'mainIMG/sample1.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'catalog_setting_id' => 1,
                'banner_path' => 'mainIMG/sample2.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'catalog_setting_id' => 1,
                'banner_path' => 'mainIMG/sample3.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 3. catalog_partner_logos
        DB::table('catalog_partner_logos')->insert([
            [
                'catalog_setting_id' => 1,
                'logo_path' => 'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__sony.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'catalog_setting_id' => 1,
                'logo_path' => 'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__canon_1.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'catalog_setting_id' => 1,
                'logo_path' => 'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__fujifilm.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'catalog_setting_id' => 1,
                'logo_path' => 'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__nikon.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'catalog_setting_id' => 1,
                'logo_path' => 'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__lumix.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'catalog_setting_id' => 1,
                'logo_path' => 'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__DJI.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'catalog_setting_id' => 1,
                'logo_path' => 'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__godox.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'catalog_setting_id' => 1,
                'logo_path' => 'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__hollyland.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'catalog_setting_id' => 1,
                'logo_path' => 'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__phottix.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'catalog_setting_id' => 1,
                'logo_path' => 'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__thinktank.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'catalog_setting_id' => 1,
                'logo_path' => 'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__om_system_update.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'catalog_setting_id' => 1,
                'logo_path' => 'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__tamron.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'catalog_setting_id' => 1,
                'logo_path' => 'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__samyang.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'catalog_setting_id' => 1,
                'logo_path' => 'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__saramonic.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'catalog_setting_id' => 1,
                'logo_path' => 'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__insta360.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'catalog_setting_id' => 1,
                'logo_path' => 'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__sigma.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
