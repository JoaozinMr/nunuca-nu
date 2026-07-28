<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    /**
     * List all orders with optional status filter and per-status counts.
     */
    public function index(Request $request): Response
    {
        $status = $request->query('status');

        $query = Order::with('items')->latest();

        if ($status && in_array($status, array_keys(Order::STATUS_LABELS), strict: true)) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(20)->withQueryString();

        // Count per-status for filter tabs
        $counts = collect(Order::STATUS_LABELS)->mapWithKeys(
            fn($label, $key) => [$key => Order::where('status', $key)->count()]
        )->toArray();

        $counts['all'] = Order::count();

        return Inertia::render('Admin/Orders/Index', [
            'orders'       => OrderResource::collection($orders),
            'counts'       => $counts,
            'statusLabels' => Order::STATUS_LABELS,
            'activeStatus' => $status ?? 'all',
        ]);
    }

    /**
     * Return full order detail as JSON (consumed by the Vue modal via Inertia visit or axios).
     */
    public function show(Order $order): \Illuminate\Http\JsonResponse
    {
        $order->load('items');

        return response()->json(new OrderResource($order));
    }

    /**
     * Advance the order to a new status.
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $order->update(['status' => $request->validated('status')]);

        return back()->with('success', "Status do pedido {$order->code} atualizado.");
    }
}
