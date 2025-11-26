<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\User;
use App\Services\StockForecastingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmartStockController extends Controller
{
    protected StockForecastingService $forecastingService;

    public function __construct(StockForecastingService $forecastingService)
    {
        $this->forecastingService = $forecastingService;
    }

    /**
     * Display the smart stock analysis page.
     */
    public function index(Request $request)
    {
        $threshold = (int) $request->input('threshold', 3);
        $sortBy = $request->input('sort', 'days_left'); // 'days_left', 'stock', 'name'
        $filter = $request->input('filter', 'all'); // 'all', 'critical', 'warning', 'safe'

        // Get all products with stock > 0
        $products = Produk::where('stok_produk', '>', 0)
            ->orderBy('nama_produk', 'asc')
            ->get();

        $analysisData = [];

        foreach ($products as $product) {
            try {
                $currentStock = (int) $product->stok_produk;
                $predictedDaysLeft = $this->forecastingService->predictStockDepletion(
                    $product->id,
                    $currentStock
                );
                $averageDailyUsage = $this->forecastingService->calculateDailyUsage($product->id);

                // Determine status
                $status = 'safe';
                if ($predictedDaysLeft <= $threshold && $predictedDaysLeft < 999) {
                    $status = $predictedDaysLeft <= 1 ? 'critical' : 'warning';
                } elseif ($predictedDaysLeft >= 999) {
                    $status = 'infinite';
                }

                $analysisData[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->nama_produk,
                    'current_stock' => $currentStock,
                    'predicted_days_left' => $predictedDaysLeft,
                    'average_daily_usage' => $averageDailyUsage,
                    'status' => $status,
                    'sku' => $product->kode_sku ?? '-',
                    'harga_jual' => (int) $product->harga_jual,
                ];
            } catch (\Exception $e) {
                // Skip products with errors
                continue;
            }
        }

        // Apply filter
        if ($filter !== 'all') {
            $analysisData = array_filter($analysisData, function ($item) use ($filter) {
                return $item['status'] === $filter;
            });
        }

        // Apply sorting
        usort($analysisData, function ($a, $b) use ($sortBy) {
            switch ($sortBy) {
                case 'stock':
                    return $b['current_stock'] <=> $a['current_stock'];
                case 'name':
                    return strcmp($a['product_name'], $b['product_name']);
                case 'days_left':
                default:
                    return $a['predicted_days_left'] <=> $b['predicted_days_left'];
            }
        });

        // Get statistics
        $stats = [
            'total_products' => count($analysisData),
            'critical' => count(array_filter($analysisData, fn($item) => $item['status'] === 'critical')),
            'warning' => count(array_filter($analysisData, fn($item) => $item['status'] === 'warning')),
            'safe' => count(array_filter($analysisData, fn($item) => $item['status'] === 'safe')),
            'infinite' => count(array_filter($analysisData, fn($item) => $item['status'] === 'infinite')),
        ];

        return view('admin.smart-stock.index', [
            'analysisData' => $analysisData,
            'stats' => $stats,
            'threshold' => $threshold,
            'sortBy' => $sortBy,
            'filter' => $filter,
        ]);
    }

    /**
     * Get stock prediction for a specific product (AJAX endpoint).
     */
    public function getProductPrediction(Request $request, $productId)
    {
        try {
            $product = Produk::findOrFail($productId);
            $currentStock = (int) $product->stok_produk;
            $predictedDaysLeft = $this->forecastingService->predictStockDepletion(
                $product->id,
                $currentStock
            );
            $averageDailyUsage = $this->forecastingService->calculateDailyUsage($product->id);

            return response()->json([
                'success' => true,
                'data' => [
                    'product_id' => $product->id,
                    'product_name' => $product->nama_produk,
                    'current_stock' => $currentStock,
                    'predicted_days_left' => $predictedDaysLeft,
                    'average_daily_usage' => $averageDailyUsage,
                    'status' => $predictedDaysLeft <= 3 ? ($predictedDaysLeft <= 1 ? 'critical' : 'warning') : 'safe',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all low stock notifications for the current user.
     */
    public function getNotifications()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated',
            ], 401);
        }

        $notifications = $user->notifications()
            ->where('type', 'App\Notifications\SmartLowStockAlert')
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                $data = $notification->data;
                return [
                    'id' => $notification->id,
                    'product_id' => $data['product_id'] ?? null,
                    'product_name' => $data['product_name'] ?? 'Unknown',
                    'current_stock' => $data['current_stock'] ?? 0,
                    'predicted_days_left' => $data['predicted_days_left'] ?? 0,
                    'message' => $data['message'] ?? '',
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $notifications,
            'count' => $notifications->count(),
        ]);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(Request $request, $notificationId)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated',
            ], 401);
        }

        $notification = $user->notifications()->find($notificationId);
        
        if ($notification) {
            $notification->markAsRead();
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Notification not found',
        ], 404);
    }
}

