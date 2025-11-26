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
    private static int $THUMB_SIZE = 300;
    private static int $MEDIUM_SIZE = 800;
    private static int $LARGE_SIZE = 1600;

    /**
     * Upload PRO version: thumbnail, medium, large
     * 
     * @param mixed $file
     * @param string $prefix
     * @return array {
     *   thumb_path, medium_path, large_path, original_hash
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

        // Convert image to webp source buffer
        $original_webp = Image::make($img)->encode("webp", 85);
        $hash = sha1($original_webp);

        $baseName = $hash . '.webp';

        // ====== Jika sudah ada versi large → semua versi sudah ada ======
        if (Storage::disk('r2')->exists("$prefix/large/$baseName")) {
            return [
                'thumb_path'  => "$prefix/thumb/$baseName",
                'medium_path' => "$prefix/medium/$baseName",
                'large_path'  => "$prefix/large/$baseName",
                'original_hash' => $hash,
            ];
        }

        // =============== GENERATE VERSI GAMBAR ===============
        $thumb  = self::resizeWebp($img, self::$THUMB_SIZE);
        $medium = self::resizeWebp($img, self::$MEDIUM_SIZE);
        $large  = self::resizeWebp($img, self::$LARGE_SIZE);

        // =============== UPLOAD ===============
        self::uploadToR2($thumb,  "$prefix/thumb/$baseName");
        self::uploadToR2($medium, "$prefix/medium/$baseName");
        self::uploadToR2($large,  "$prefix/large/$baseName");

        return [
            'thumb_path'    => "$prefix/thumb/$baseName",
            'medium_path'   => "$prefix/medium/$baseName",
            'large_path'    => "$prefix/large/$baseName",
            'original_hash' => $hash,
        ];
    }

    /**
     * Resize helper (ke WEBP buffer)
     */
    private static function resizeWebp($img, int $maxWidth)
    {
        $clone = Image::make($img);
        $clone->resize($maxWidth, null, function ($c) {
            $c->aspectRatio();
            $c->upsize();
        });
        return $clone->encode("webp", 85);
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
