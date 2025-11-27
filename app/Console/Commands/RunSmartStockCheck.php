<?php

namespace App\Console\Commands;

use App\Models\Produk;
use App\Models\User;
use App\Notifications\SmartLowStockAlert;
use App\Services\StockForecastingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RunSmartStockCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:check-smart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check stock levels and send smart low stock alerts based on sales forecasting';

    /**
     * The stock forecasting service instance.
     *
     * @var StockForecastingService
     */
    protected StockForecastingService $forecastingService;

    /**
     * Create a new command instance.
     */
    public function __construct(StockForecastingService $forecastingService)
    {
        parent::__construct();
        $this->forecastingService = $forecastingService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting Smart Stock Check...');
        $this->newLine();

        // Get all products with stock > 0
        $products = Produk::where('stok_produk', '>', 0)->get();

        if ($products->isEmpty()) {
            $this->warn('No products with stock found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$products->count()} products to check.");
        $this->newLine();

        // Get admin user (role 'admin' or id 1)
        // Priority: First try to find user with role 'admin', then fallback to id 1
        $admin = User::where('role', 'admin')->first();
        
        if (!$admin) {
            $admin = User::find(1);
        }

        if (!$admin) {
            $this->error('Admin user not found. Cannot send notifications.');
            return Command::FAILURE;
        }

        $this->info("Notifications will be sent to: {$admin->name} (ID: {$admin->id})");
        $this->newLine();

        // Threshold for low stock alert (days)
        $threshold = 3;
        $alertsSent = 0;
        $errors = 0;

        // Create progress bar
        $bar = $this->output->createProgressBar($products->count());
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% -- %message%');
        $bar->setMessage('Processing products...');
        $bar->start();

        foreach ($products as $product) {
            try {
                $bar->setMessage("Checking: {$product->nama_produk}");

                // Get current stock
                $currentStock = (int) $product->stok_produk;

                // Predict days until depletion
                $predictedDaysLeft = $this->forecastingService->predictStockDepletion(
                    $product->id,
                    $currentStock
                );

                // Check if stock will last less than threshold days
                if ($predictedDaysLeft <= $threshold && $predictedDaysLeft < 999) {
                    // Send notification to admin
                    $admin->notify(new SmartLowStockAlert(
                        $product->id,
                        $product->nama_produk,
                        $currentStock,
                        $predictedDaysLeft
                    ));

                    $alertsSent++;
                    $this->newLine();
                    $this->warn("  ⚠️  Alert sent: {$product->nama_produk} - {$predictedDaysLeft} days left");
                }

            } catch (\Exception $e) {
                $errors++;
                Log::error('Smart Stock Check Error', [
                    'product_id' => $product->id ?? null,
                    'product_name' => $product->nama_produk ?? 'Unknown',
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                $this->newLine();
                $this->error("  ❌ Error processing product: {$product->nama_produk}");
                $this->error("     {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info('========================================');
        $this->info('  SMART STOCK CHECK SUMMARY');
        $this->info('========================================');
        $this->line("Total products checked: {$products->count()}");
        $this->line("Alerts sent: <fg=green>{$alertsSent}</>");
        $this->line("Errors encountered: " . ($errors > 0 ? "<fg=red>{$errors}</>" : "<fg=green>{$errors}</>"));
        $this->info('========================================');

        if ($errors > 0) {
            $this->warn("⚠️  Some products encountered errors. Check logs for details.");
        }

        return Command::SUCCESS;
    }
}

