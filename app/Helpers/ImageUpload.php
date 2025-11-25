<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Http\File;

class ImageUpload
{
    /**
     * Upload gambar terbaik → WebP + Resize + Hash check
     *
     * @param mixed $file   file input dari form
     * @param string $prefix folder tujuan R2 (misal: catalog/logo)
     * @return string        path di Cloudflare R2
     */
    public static function upload($file, string $prefix = 'uploads'): string
    {
        // ---- 1. SIAPKAN GAMBAR ----
        $image = Image::make($file);

        // Resize maksimum 1200px agar hemat bandwidth
        $image->resize(1200, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Convert ke WEBP kualitas 80% (tampang masih bagus banget)
        $image->encode('webp', 80);

        // ---- 2. BUAT HASH untuk CEK DUPLIKAT ----
        $hash = sha1($image);
        $filename = $hash . '.webp';
        $remotePath = $prefix . '/' . $filename;

        // Jika file sudah ada di R2 → langsung balikin
        if (Storage::disk('r2')->exists($remotePath)) {
            return $remotePath;
        }

        // ---- 3. SIMPAN SEMENTARA → UPLOAD R2 ----
        $tempFile = sys_get_temp_dir() . '/' . $filename;
        $image->save($tempFile);

        Storage::disk('r2')->putFileAs(
            $prefix,
            new File($tempFile),
            $filename,
            [
                'visibility' => 'public',
                'ContentType' => 'image/webp',
                'CacheControl' => 'public, max-age=31536000, immutable'
            ]
        );

        // hapus temp
        @unlink($tempFile);

        return $remotePath;
    }
}
