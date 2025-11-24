<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\File;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $localDir = base_path('public/mainIMG');

        if (!is_dir($localDir)) {
            throw new \RuntimeException("Folder mainIMG tidak ditemukan: {$localDir}");
        }

        // ======================================================
        // 1) SEED catalog_settings
        // ======================================================
        $catalogId = DB::table('catalog_settings')->insertGetId([
            'nama_website'    => 'Dinoyo Kamera',
            'nomor_telfon'    => '082345670014',
            'description'     => 'Toko kamera terpercaya di Malang dan Pasuruan. Menyediakan berbagai kebutuhan fotografi dan videografi dengan kualitas terjamin.',
            'logo_path'       => null, // akan diupdate setelah upload
            'facebook_link'   => 'https://www.facebook.com/jualkameramalang',
            'youtube_link'    => 'https://www.youtube.com/@DINOYOKAMERA_OFFICIAL',
            'instagram_link'  => 'https://www.instagram.com/dinoyokamera/',
            'tiktok_link'     => 'https://www.tiktok.com/@dinoyokamera',
            'tokopedia_link'  => 'https://www.tokopedia.com/dinoyo-kamera',
            'shopee_link'     => 'https://shopee.co.id/dinoyokamera',
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);


        // ======================================================
        // 2) UPLOAD LOGO
        // ======================================================
        $logoPath = $this->uploadLocalToR2("logoDinoyo.png", $localDir, "catalog/logo");

        DB::table('catalog_settings')->where('id', $catalogId)->update([
            'logo_path' => $logoPath
        ]);


        // ======================================================
        // 3) UPLOAD BANNER
        // ======================================================
        $bannerFiles = [
            "sample1.png",
            "sample2.png",
            "sample3.png",
        ];

        foreach ($bannerFiles as $file) {

            $uploaded = $this->uploadLocalToR2($file, $localDir, "catalog/banners");

            DB::table('catalog_banners')->insert([
                'catalog_setting_id' => $catalogId,
                'banner_path'        => $uploaded,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        }


        // ======================================================
        // 4) UPLOAD PARTNER LOGO (dari URL)
        // ======================================================
        $partnerUrls = [
            'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__sony.jpg',
            'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__canon_1.jpg',
            'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__fujifilm.jpg',
            'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__nikon.jpg',
            'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__lumix.jpg',
            'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__DJI.jpg',
            'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__godox.jpg',
            'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__hollyland.jpg',
            'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__phottix.jpg',
            'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__thinktank.jpg',
            'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__om_system_update.jpg',
            'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__tamron.jpg',
            'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__samyang.jpg',
            'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__saramonic.jpg',
            'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__insta360.jpg',
            'https://admin.focusnusantara.com/media/wysiwyg/brands/Logo_All_Brand_Home_-__sigma.jpg',
        ];

        foreach ($partnerUrls as $url) {
            $uploaded = $this->uploadUrlToR2($url, "catalog/partners");

            DB::table('catalog_partner_logos')->insert([
                'catalog_setting_id' => $catalogId,
                'logo_path'          => $uploaded,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        }
    }


    // ============================================================
    // UPLOAD FILE LOKAL → R2
    // ============================================================
    private function uploadLocalToR2(string $filename, string $localDir, string $prefix): string
    {
        $source = $localDir . DIRECTORY_SEPARATOR . $filename;

        if (!is_file($source)) {
            throw new \RuntimeException("Seeder image not found: {$source}");
        }

        // Hash isi file → nama file selalu konsisten
        $hash = sha1_file($source);
        $ext = strtolower(pathinfo($source, PATHINFO_EXTENSION));
        $remoteName = "{$hash}.{$ext}";
        $remotePath = "{$prefix}/{$remoteName}";

        // Jika sudah ada → return
        if (Storage::disk('r2')->exists($remotePath)) {
            return $remotePath;
        }

        Storage::disk('r2')->putFileAs(
            $prefix,
            new File($source),
            $remoteName,
            [
                'visibility' => 'public',
                'CacheControl' => 'public, max-age=31536000, immutable'
            ]
        );

        return $remotePath;
    }


    // ============================================================
    // UPLOAD FILE DARI URL → R2
    // ============================================================
    private function uploadUrlToR2(string $url, string $prefix): string
    {
        $temp = tempnam(sys_get_temp_dir(), 'img_');
        file_put_contents($temp, file_get_contents($url));

        // Hash isi file
        $hash = sha1_file($temp);
        $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
        $remoteName = "{$hash}.{$ext}";
        $remotePath = "{$prefix}/{$remoteName}";

        // Jika sudah ada → cleanup + return
        if (Storage::disk('r2')->exists($remotePath)) {
            @unlink($temp);
            return $remotePath;
        }

        Storage::disk('r2')->putFileAs(
            $prefix,
            new File($temp),
            $remoteName,
            [
                'visibility' => 'public',
                'CacheControl' => 'public, max-age=31536000, immutable'
            ]
        );

        @unlink($temp);

        return $remotePath;
    }
}
