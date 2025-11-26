<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
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
        // ---- 0. VALIDASI UKURAN FILE (Maksimal 5MB) ----
        $maxFileSize = 5 * 1024 * 1024; // 5MB dalam bytes
        $fileSize = is_string($file) ? filesize($file) : $file->getSize();
        
        if ($fileSize > $maxFileSize) {
            $fileSizeMB = round($fileSize / (1024 * 1024), 2);
            throw new \Exception(
                "Ukuran file terlalu besar ({$fileSizeMB}MB). Maksimal ukuran file adalah 5MB."
            );
        }

        // ---- 1. INCREASE MEMORY LIMIT untuk gambar besar ----
        $originalMemoryLimit = ini_get('memory_limit');
        $memoryLimitBytes = self::convertToBytes($originalMemoryLimit);
        // Tingkatkan ke 1GB untuk gambar sangat besar
        $minRequiredMemory = 1024 * 1024 * 1024; // 1GB minimum
        
        if ($memoryLimitBytes < $minRequiredMemory) {
            @ini_set('memory_limit', '1G');
        }

        try {
            // ---- 2. CEK DIMENSI FILE SEBELUM LOAD KE MEMORY ----
            // Get image info without loading full image into memory
            $imageInfo = @getimagesize($file);
            
            if ($imageInfo === false) {
                throw new \Exception('File bukan gambar yang valid');
            }

            $originalWidth = $imageInfo[0];
            $originalHeight = $imageInfo[1];
            $imageType = $imageInfo[2]; // IMAGETYPE_JPEG, IMAGETYPE_PNG, etc.
            
            // Hitung estimasi memory yang dibutuhkan (untuk PNG dengan alpha channel bisa lebih besar)
            // Formula: width * height * channels * bytes_per_channel
            // PNG dengan alpha = 4 channels, JPEG = 3 channels
            $channels = ($imageType === IMAGETYPE_PNG) ? 4 : 3; // PNG bisa punya alpha
            $bytesPerChannel = 1; // 8-bit per channel
            $estimatedMemoryNeeded = $originalWidth * $originalHeight * $channels * $bytesPerChannel;
            // Tambahkan overhead untuk processing (sekitar 2-3x)
            $estimatedMemoryNeeded = $estimatedMemoryNeeded * 3;
            
            // Jika estimasi memory terlalu besar, reject atau resize dulu
            $maxMemoryEstimate = 800 * 1024 * 1024; // 800MB max estimate
            if ($estimatedMemoryNeeded > $maxMemoryEstimate) {
                // Gambar terlalu besar, kita perlu resize dulu menggunakan pendekatan yang lebih hemat
                // Gunakan command line tools jika tersedia, atau resize dengan cara lain
                throw new \Exception(
                    "Gambar terlalu besar ({$originalWidth}x{$originalHeight}px). " .
                    "Mohon resize gambar terlebih dahulu maksimal 6000x6000px atau gunakan format JPEG."
                );
            }

            // ---- 3. COBA GUNAKAN IMAGICK JIKA TERSEDIA (lebih efisien untuk gambar besar) ----
            $useImagick = extension_loaded('imagick');
            
            if ($useImagick) {
                // Gunakan Imagick driver yang lebih efisien untuk gambar besar
                $manager = new ImageManager(['driver' => 'imagick']);
                $image = $manager->make($file);
            } else {
                // Fallback ke GD, tapi pastikan memory cukup
                // Limit maksimum dimensi untuk GD (lebih konservatif)
                $maxDimensionForGD = 5000; // Maksimum 5000px untuk GD
                if ($originalWidth > $maxDimensionForGD || $originalHeight > $maxDimensionForGD) {
                    throw new \Exception(
                        "Gambar terlalu besar untuk diproses ({$originalWidth}x{$originalHeight}px). " .
                        "Mohon install PHP Imagick extension atau resize gambar terlebih dahulu maksimal 5000x5000px."
                    );
                }
                
                $image = Image::make($file);
            }

            // Resize maksimum 1200px untuk output final agar hemat bandwidth
            $image->resize(1200, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // Convert ke WEBP kualitas 80% (tampang masih bagus banget)
            $image->encode('webp', 80);

            // ---- 4. BUAT HASH untuk CEK DUPLIKAT ----
            $hash = sha1($image);
            $filename = $hash . '.webp';
            $remotePath = $prefix . '/' . $filename;

            // Jika file sudah ada di R2 → langsung balikin
            if (Storage::disk('r2')->exists($remotePath)) {
                // Clean up memory
                $image->destroy();
                return $remotePath;
            }

            // ---- 5. SIMPAN SEMENTARA → UPLOAD R2 ----
            $tempFile = sys_get_temp_dir() . '/' . Str::random(16) . '_' . $filename;
            $image->save($tempFile);
            
            // Clean up memory immediately after saving
            $image->destroy();
            unset($image);

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

        } catch (\Throwable $th) {
            // Restore original memory limit if we changed it
            if (isset($originalMemoryLimit)) {
                @ini_set('memory_limit', $originalMemoryLimit);
            }
            throw $th;
        } finally {
            // Restore original memory limit
            if (isset($originalMemoryLimit)) {
                @ini_set('memory_limit', $originalMemoryLimit);
            }
        }
    }

    /**
     * Convert memory limit string to bytes
     * 
     * @param string $value Memory limit string (e.g., "128M", "512M", "1G")
     * @return int Memory limit in bytes
     */
    private static function convertToBytes(string $value): int
    {
        $value = trim($value);
        $last = strtolower($value[strlen($value) - 1]);
        $value = (int) $value;

        switch ($last) {
            case 'g':
                $value *= 1024;
                // no break
            case 'm':
                $value *= 1024;
                // no break
            case 'k':
                $value *= 1024;
        }

        return $value;
    }
}
