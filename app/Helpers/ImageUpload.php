<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Str;

class ImageUpload
{

    private static int $MAX_DIMENSION = 5000;
    private static int $WEBP_QUALITY = 92;

    /**
     * Upload single optimized image (WebP)
     * R2 falls back to local storage; disk public stores locally without accessing R2.
     *
     * @param mixed $file
     * @param string $prefix
     * @param string $disk r2 or public
     * @return array {
     *   path, original_hash
     * }
     */
    public static function upload($file, string $prefix = 'uploads', string $disk = 'r2'): array
    {
        if (!in_array($disk, ['r2', 'public'], true)) {
            throw new \InvalidArgumentException('Disk upload gambar tidak didukung.');
        }

        $info = @getimagesize($file);
        if (!$info) {
            throw new \Exception("File bukan gambar valid.");
        }

        $w = $info[0];
        $h = $info[1];

        if ($w > self::$MAX_DIMENSION || $h > self::$MAX_DIMENSION) {
            throw new \Exception("Resolusi terlalu besar ({$w}x{$h}). Maksimal 5000px.");
        }

        $img = Image::make($file)->orientate();

        $encoded = Image::make($img)->encode("webp", self::$WEBP_QUALITY);
        $hash = sha1($encoded);

        $baseName = $hash . '.webp';
        $path = "$prefix/$baseName";

        if ($disk === 'public') {
            return self::uploadToLocal($encoded, $path);
        }

        // PERBAIKAN: Try R2 first, fallback to local
        try {
            if (Storage::disk('r2')->exists($path)) {
                return [
                    'path'          => $path,
                    'original_hash' => $hash,
                ];
            }

            // Try upload to R2
            self::uploadToR2($encoded, $path);

            return [
                'path'          => $path,
                'original_hash' => $hash,
            ];
        } catch (\Throwable $e) {
            // PERBAIKAN: Fallback ke local storage jika R2 gagal
            \Log::warning("R2 upload failed, fallback to local storage", [
                'error' => $e->getMessage(),
                'path' => $path
            ]);

            return self::uploadToLocal($encoded, $path);
        }
    }

    /**
     * Upload buffer image → Cloudflare R2
     */
    private static function uploadToR2($buffer, string $path): void
    {
        $temp = sys_get_temp_dir().'/'.Str::uuid().'.webp';
        $buffer->save($temp);

        try {
            $stored = Storage::disk("r2")->putFileAs(
                dirname($path),
                new File($temp),
                basename($path),
                [
                    'visibility' => 'public',
                    'ContentType' => 'image/webp',
                    'CacheControl' => 'public, max-age=31536000, immutable'
                ]
            );
            if ($stored === false) {
                throw new \RuntimeException('Upload gambar ke R2 gagal.');
            }
        } finally {
            @unlink($temp);
        }
    }

    /**
     * PERBAIKAN: Upload ke local storage sebagai fallback
     */
    private static function uploadToLocal($buffer, string $path): array
    {
        $temp = sys_get_temp_dir().'/'.Str::uuid().'.webp';
        $buffer->save($temp);

        try {
            $localDisk = Storage::disk('public');

            // Ensure directory exists
            $dir = dirname($path);
            if (!$localDisk->exists($dir)) {
                $localDisk->makeDirectory($dir, 0755, true);
            }

            // Upload to public storage
            $stored = $localDisk->putFileAs(
                $dir,
                new File($temp),
                basename($path),
                [
                    'visibility' => 'public',
                ]
            );
            if ($stored === false) {
                throw new \RuntimeException('Upload gambar ke penyimpanan lokal gagal.');
            }

            // Return public accessible path
            return [
                'path'          => "public/{$path}",
                'original_hash' => null,
            ];
        } finally {
            @unlink($temp);
        }
    }
}
