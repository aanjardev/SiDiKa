<?php

namespace App\Jobs;

use App\Models\Produk;
use App\Models\GambarProduk;
use App\Models\User;
use App\Notifications\JobCompleted;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessProductImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The maximum number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300; // 5 minutes for image processing

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $productId,
        public array $temporaryFilePaths,
        public ?int $mainImageIndex = null,
        public ?int $userId = null
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::beginTransaction();
        try {
            $product = Produk::findOrFail($this->productId);
            $hasMain = $product->gambarUtama()->exists();
            $createdIds = [];

            foreach ($this->temporaryFilePaths as $index => $tempPath) {
                // Check if temporary file exists
                if (!Storage::disk('local')->exists($tempPath)) {
                    Log::warning("Temporary file not found: {$tempPath}");
                    continue;
                }

                // Get file from temporary storage
                $tempFilePath = Storage::disk('local')->path($tempPath);
                
                try {
                    // Use ImageUpload helper to process and optimize image
                    // This will handle resizing, compression, WebP conversion, and memory management
                    // ImageUpload will automatically increase memory limit and use Imagick if available
                    $prefix = 'product/' . $product->id;
                    $permanentPath = \App\Helpers\ImageUpload::upload(
                        $tempFilePath,
                        $prefix
                    );

                    // Delete temporary file
                    Storage::disk('local')->delete($tempPath);

                    // Create database record
                    $gambar = GambarProduk::create([
                        'id_produk' => $product->id,
                        'path_gambar' => $permanentPath,
                        'is_main' => false,
                    ]);

                    $createdIds[] = [
                        'id' => $gambar->id,
                        'index' => $index
                    ];
                } catch (\Throwable $e) {
                    Log::error("Failed to process image: {$tempPath}", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    // Delete temporary file even if processing failed
                    try {
                        if (Storage::disk('local')->exists($tempPath)) {
                            Storage::disk('local')->delete($tempPath);
                        }
                    } catch (\Throwable $deleteError) {
                        Log::warning("Failed to delete temporary file: {$tempPath}", [
                            'error' => $deleteError->getMessage()
                        ]);
                    }
                    
                    // Continue with next image instead of failing entire job
                    // But log the error so user knows which images failed
                    continue;
                }
            }

            // Handle main image selection
            if ($this->mainImageIndex !== null) {
                $mainRecord = null;
                foreach ($createdIds as $created) {
                    if (($created['index'] ?? null) === $this->mainImageIndex) {
                        $mainRecord = $created;
                        break;
                    }
                }

                if ($mainRecord) {
                    GambarProduk::where('id_produk', $product->id)->update(['is_main' => false]);
                    GambarProduk::where('id', $mainRecord['id'])->update(['is_main' => true]);
                }
            } elseif (!$hasMain && !empty($createdIds)) {
                // Set first image as main if no main exists
                GambarProduk::where('id', $createdIds[0]['id'])->update(['is_main' => true]);
            }

            DB::commit();

            // Send notification to user if userId is provided
            if ($this->userId) {
                $user = User::find($this->userId);
                if ($user) {
                    $user->notify(new JobCompleted(
                        'Upload Foto Produk',
                        "Foto produk '{$product->nama_produk}' berhasil diunggah dan diproses.",
                        'success'
                    ));
                }
            }

            Log::info("Product images processed successfully for product ID: {$this->productId}");

        } catch (\Throwable $th) {
            DB::rollBack();
            
            // Clean up any remaining temporary files
            $this->cleanupTemporaryFiles();
            
            Log::error("Failed to process product images: " . $th->getMessage(), [
                'product_id' => $this->productId,
                'trace' => $th->getTraceAsString()
            ]);
            
            throw $th;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        // Clean up temporary files
        $this->cleanupTemporaryFiles();

        // Send failure notification to user
        if ($this->userId) {
            try {
                $user = User::find($this->userId);
                if ($user) {
                    $user->notify(new JobCompleted(
                        'Upload Foto Produk Gagal',
                        "Gagal mengunggah foto produk. Silakan coba lagi atau hubungi administrator.",
                        'error'
                    ));
                }
            } catch (\Throwable $e) {
                Log::error("Failed to send failure notification: " . $e->getMessage());
            }
        }

        Log::error("Job ProcessProductImage failed permanently", [
            'product_id' => $this->productId,
            'error' => $exception->getMessage()
        ]);
    }

    /**
     * Clean up temporary files.
     */
    private function cleanupTemporaryFiles(): void
    {
        foreach ($this->temporaryFilePaths as $tempPath) {
            try {
                if (Storage::disk('local')->exists($tempPath)) {
                    Storage::disk('local')->delete($tempPath);
                }
            } catch (\Throwable $e) {
                Log::warning("Failed to delete temporary file: {$tempPath}", [
                    'error' => $e->getMessage()
                ]);
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
