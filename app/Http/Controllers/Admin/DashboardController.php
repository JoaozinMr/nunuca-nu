<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $today     = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();

        // ── Revenue-counting statuses (exclude pending & canceled) ─────────────
        $revenueStatuses = [
            Order::STATUS_PREPARING,
            Order::STATUS_OUT_FOR_DELIVERY,
            Order::STATUS_COMPLETED,
        ];

        // ── KPI cards ──────────────────────────────────────────────────────────
        $stats = [
            'revenue_today'   => (float) Order::whereIn('status', $revenueStatuses)
                ->whereDate('created_at', $today)
                ->sum('total'),

            'revenue_monthly' => (float) Order::whereIn('status', $revenueStatuses)
                ->where('created_at', '>=', $monthStart)
                ->sum('total'),

            'orders_today'    => Order::whereDate('created_at', $today)->count(),

            'orders_total'    => Order::count(),

            'pending_count'   => Order::where('status', Order::STATUS_PENDING)->count(),

            'preparing_count' => Order::where('status', Order::STATUS_PREPARING)->count(),

            'delivery_count'  => Order::where('status', Order::STATUS_OUT_FOR_DELIVERY)->count(),

            'low_stock_count' => Product::lowStock()->count(),
        ];

        // ── Recent orders (last 12) ────────────────────────────────────────────
        $recentOrders = Order::with('items')
            ->latest()
            ->take(12)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats'        => $stats,
            'recentOrders' => OrderResource::collection($recentOrders),
            'statusLabels' => Order::STATUS_LABELS,
        ]);
    }
}
