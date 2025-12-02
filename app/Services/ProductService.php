<?php

namespace App\Services;

use App\Jobs\CleanupProductAssets;
use App\Jobs\ProcessProductImage;
use App\Models\GambarProduk;
use App\Models\Produk;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function createProduct(array $data, array $images, ?string $mainImageInput, ?int $userId = null): Produk
    {
        $tempPaths = $this->storeTemporaryImages($images, 'temp/product-creates');
        $selectedMainIndex = $this->extractNewMainIndex($mainImageInput);

        DB::beginTransaction();
        try {
            $product = Produk::create([
                'nama_produk'      => $data['nama_produk'],
                'kode_sku'         => $data['kode_sku'],
                'id_kategori'      => $data['id_kategori'],
                'harga_jual'       => $data['harga_jual'] ?? null,
                'harga_beli'       => $data['harga_beli'] ?? null,
                'harga_servis'     => $data['harga_servis'] ?? null,
                'stok_produk'      => $data['stok_produk'] ?? null,
                'deskripsi_produk' => $data['deskripsi_produk'] ?? null,
                'status'           => $data['status'],
                'grade'            => $data['grade'],
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->cleanupTemporaryUploads($tempPaths);
            throw $th;
        }

        if (!empty($tempPaths)) {
            ProcessProductImage::dispatch(
                $product->id,
                $tempPaths,
                $selectedMainIndex ?? 0,
                $userId
            );
        }

        return $product;
    }

    public function updateProduct(Produk $product, array $data, array $newImages, ?string $mainImageInput, array $removeImages = [], ?int $userId = null): void
    {
        $tempPaths = $this->storeTemporaryImages($newImages, 'temp/product-updates');
        $selectedMainNewIndex = $this->extractNewMainIndex($mainImageInput);
        $selectedMainExistingId = $this->extractExistingMainId($mainImageInput);
        $pathsForCleanup = [];

        DB::beginTransaction();
        try {
            $product->update([
                'nama_produk'      => $data['nama_produk'],
                'kode_sku'         => $data['kode_sku'],
                'id_kategori'      => $data['id_kategori'],
                'harga_jual'       => $data['harga_jual'] ?? null,
                'harga_beli'       => $data['harga_beli'] ?? null,
                'harga_servis'     => $data['harga_servis'] ?? null,
                'stok_produk'      => $data['stok_produk'] ?? null,
                'deskripsi_produk' => $data['deskripsi_produk'] ?? null,
                'status'           => $data['status'],
                'grade'            => $data['grade'],
            ]);

            $removeIds = array_filter($removeImages, fn ($v) => !empty($v));
            if (!empty($removeIds)) {
                $imagesToDelete = GambarProduk::whereIn('id', $removeIds)
                    ->where('id_produk', $product->id)
                    ->get();

                foreach ($imagesToDelete as $img) {
                    $pathsForCleanup[] = $img->path_gambar;
                    $img->delete();
                }
            }

            if ($selectedMainExistingId !== null) {
                $targetImage = $product->gambar()->where('id', $selectedMainExistingId)->first();
                if ($targetImage) {
                    GambarProduk::where('id_produk', $product->id)->update(['is_main' => false]);
                    $targetImage->is_main = true;
                    $targetImage->save();
                }
            }

            if (!$product->gambarUtama()->exists()) {
                $first = $product->gambar()->first();
                if ($first) {
                    $first->update(['is_main' => true]);
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->cleanupTemporaryUploads($tempPaths);
            throw $th;
        }

        if (!empty($pathsForCleanup)) {
            CleanupProductAssets::dispatch($pathsForCleanup);
        }

        if (!empty($tempPaths)) {
            $product->refresh();
            $productHasMain = $product->gambarUtama()->exists();

            ProcessProductImage::dispatch(
                $product->id,
                $tempPaths,
                $selectedMainNewIndex !== null ? $selectedMainNewIndex : ($productHasMain ? null : 0),
                $userId
            );
        }
    }

    public function uploadAdditionalPhotos(Produk $product, array $images, ?string $mainImageInput, ?int $userId = null): void
    {
        if (empty($images)) {
            throw new \InvalidArgumentException('Tidak ada gambar yang diunggah.');
        }

        // Handle existing main image selection (before queue processing)
        if (!empty($mainImageInput) && str_starts_with($mainImageInput, 'existing_')) {
            $idToSet = intval(substr($mainImageInput, strlen('existing_')));
            GambarProduk::where('id_produk', $product->id)->update(['is_main' => false]);
            GambarProduk::where('id_produk', $product->id)
                ->where('id', $idToSet)
                ->update(['is_main' => true]);
        }

        $tempPaths = $this->storeTemporaryImages($images, 'temp/product-uploads');
        $mainImageIndex = $this->extractNewMainIndex($mainImageInput);

        ProcessProductImage::dispatch(
            $product->id,
            $tempPaths,
            $mainImageIndex,
            $userId
        );
    }

    private function storeTemporaryImages(array $images, string $folder): array
    {
        $temporaryPaths = [];
        foreach ($images as $image) {
            $temporaryPaths[] = $image->store($folder, 'local');
        }
        return $temporaryPaths;
    }

    private function cleanupTemporaryUploads(array $paths): void
    {
        foreach ($paths as $tempPath) {
            try {
                if (Storage::disk('local')->exists($tempPath)) {
                    Storage::disk('local')->delete($tempPath);
                }
            } catch (\Throwable $th) {
                \Log::warning('Failed deleting temporary upload', [
                    'path' => $tempPath,
                    'error' => $th->getMessage(),
                ]);
            }
        }
    }

    private function extractNewMainIndex(?string $mainImageInput): ?int
    {
        if (!empty($mainImageInput) && str_starts_with($mainImageInput, 'new_')) {
            return intval(substr($mainImageInput, strlen('new_')));
        }
        return null;
    }

    private function extractExistingMainId(?string $mainImageInput): ?int
    {
        if (!empty($mainImageInput) && str_starts_with($mainImageInput, 'existing_')) {
            return intval(substr($mainImageInput, strlen('existing_')));
        }
        return null;
    }
}
