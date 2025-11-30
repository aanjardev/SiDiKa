<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Str;

class ImageUpload
{
    // ==== CONFIG ====
    private static int $MAX_DIMENSION = 5000;
    private static int $WEBP_QUALITY = 92;

    /**
     * Upload single optimized image (WebP)
     * 
     * @param mixed $file
     * @param string $prefix
     * @return array {
     *   path, original_hash
     * }
     */
    public static function upload($file, string $prefix = 'uploads'): array
    {
        // =============== VALIDASI DASAR TANPA LOAD BESAR-BESAR ===============
        $info = @getimagesize($file);
        if (!$info) {
            throw new \Exception("File bukan gambar valid.");
        }

        $w = $info[0];
        $h = $info[1];

        if ($w > self::$MAX_DIMENSION || $h > self::$MAX_DIMENSION) {
            throw new \Exception("Resolusi terlalu besar ({$w}x{$h}). Maksimal 5000px.");
        }

        // =============== LOAD GAMBAR (AMAN) ===============
        $img = Image::make($file)->orientate();

        // Keep original resolution (max 5000px) with gentler compression
        $encoded = Image::make($img)->encode("webp", self::$WEBP_QUALITY);
        $hash = sha1($encoded);

        $baseName = $hash . '.webp';
        $path = "$prefix/$baseName";

        // ====== If image already exists, return existing path ======
        if (Storage::disk('r2')->exists($path)) {
            return [
                'path'          => $path,
                'original_hash' => $hash,
            ];
        }

        // =============== UPLOAD ===============
        self::uploadToR2($encoded, $path);

        return [
            'path'          => $path,
            'original_hash' => $hash,
        ];
    }

    /**
     * Upload buffer image → Cloudflare R2
     */
    private static function uploadToR2($buffer, string $path): void
    {
        $temp = sys_get_temp_dir().'/'.Str::uuid().'.webp';
        $buffer->save($temp);

        Storage::disk("r2")->putFileAs(
            dirname($path),
            new File($temp),
            basename($path),
            [
                'visibility' => 'public',
                'ContentType' => 'image/webp',
                'CacheControl' => 'public, max-age=31536000, immutable'
            ]
        );

        @unlink($temp);
    }
}
