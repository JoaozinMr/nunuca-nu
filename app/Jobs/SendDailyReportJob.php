<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Order;
use App\Models\Product;
use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Compiles daily business KPIs and sends a summary report via Telegram.
 *
 * Dispatched automatically each morning via the scheduler (see routes/console.php).
 * Can also be dispatched manually:
 *   php artisan queue:work && php artisan dispatch:daily-report
 */
class SendDailyReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [30, 120];
    public int $timeout = 60;

    public function handle(TelegramService $telegram): void
    {
        $today    = Carbon::today();
        $timezone = config('app.timezone', 'America/Sao_Paulo');

        // ── Today's numbers ───────────────────────────────────────────────────
        $todaysOrders = Order::whereDate('created_at', $today)->get();
        $ordersToday  = $todaysOrders->count();
        $revenueToday = $todaysOrders
            ->whereIn('status', [Order::STATUS_COMPLETED, Order::STATUS_PREPARING, Order::STATUS_OUT_FOR_DELIVERY])
            ->sum('total');
        $completedToday = $todaysOrders->where('status', Order::STATUS_COMPLETED)->count();

        // ── Running totals ────────────────────────────────────────────────────
        $allOrders    = Order::all();
        $ordersTotal  = $allOrders->count();
        $revenueTotal = $allOrders
            ->whereIn('status', [Order::STATUS_COMPLETED, Order::STATUS_PREPARING, Order::STATUS_OUT_FOR_DELIVERY])
            ->sum('total');

        // ── Open statuses ─────────────────────────────────────────────────────
        $pending        = Order::where('status', Order::STATUS_PENDING)->count();
        $preparing      = Order::where('status', Order::STATUS_PREPARING)->count();
        $outForDelivery = Order::where('status', Order::STATUS_OUT_FOR_DELIVERY)->count();

        // ── Low stock products ────────────────────────────────────────────────
        $lowStockProducts = Product::lowStock()
            ->get()
            ->map(fn(Product $p) => "{$p->name} ({$p->flavor}) — estoque: {$p->stock}/{$p->min_stock}")
            ->toArray();

        $telegram->sendDailyReport([
            'date'             => now()->timezone($timezone)->format('d/m/Y'),
            'orders_today'     => $ordersToday,
            'revenue_today'    => (float) $revenueToday,
            'completed_today'  => $completedToday,
            'pending'          => $pending,
            'preparing'        => $preparing,
            'out_for_delivery' => $outForDelivery,
            'orders_total'     => $ordersTotal,
            'revenue_total'    => (float) $revenueTotal,
            'low_stock'        => $lowStockProducts,
        ]);

        Log::info('SendDailyReportJob: report dispatched.', [
            'orders_today'  => $ordersToday,
            'revenue_today' => $revenueToday,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SendDailyReportJob: failed to send daily report.', [
            'error' => $exception->getMessage(),
        ]);
    }
}
