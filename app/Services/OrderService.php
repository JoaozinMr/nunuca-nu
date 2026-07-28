<?php

declare(strict_types=1);

namespace App\Services;

use App\Jobs\NotifyNewOrderJob;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Encapsulates the business logic for creating an order from a client-side cart.
 *
 * Thin controllers delegate here; this class owns:
 *   - Product availability checks
 *   - Total calculation
 *   - Atomic DB writes (Order + OrderItems)
 *   - Job dispatch
 */
class OrderService
{
    /**
     * Create a new order from validated checkout data.
     *
     * @param  array{
     *   customer_name: string,
     *   customer_phone: string,
     *   customer_email: ?string,
     *   delivery_type: string,
     *   address: ?string,
     *   notes: ?string,
     *   items: array<array{product_id: int, quantity: int}>
     * } $data
     *
     * @throws \Illuminate\Validation\ValidationException if any product is unavailable
     */
    public function createFromCart(array $data): Order
    {
        // ── 1. Hydrate and verify products ────────────────────────────────────
        $productIds = collect($data['items'])->pluck('product_id')->unique();
        $products   = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $unavailable = [];
        foreach ($data['items'] as $item) {
            $product = $products->get($item['product_id']);
            if (! $product || ! $product->is_available) {
                $unavailable[] = $product?->name ?? "Produto #{$item['product_id']}";
            }
        }

        if (! empty($unavailable)) {
            throw ValidationException::withMessages([
                'items' => 'Os seguintes produtos não estão disponíveis: ' . implode(', ', $unavailable),
            ]);
        }

        // ── 2. Compute totals ─────────────────────────────────────────────────
        $total = 0.0;
        $lineItems = [];

        foreach ($data['items'] as $item) {
            $product  = $products->get($item['product_id']);
            $qty      = (int) $item['quantity'];
            $subtotal = round((float) $product->price * $qty, 2);
            $total   += $subtotal;

            $lineItems[] = [
                'product_id'     => $product->id,
                'product_name'   => $product->name,
                'product_flavor' => $product->flavor,
                'unit_price'     => (float) $product->price,
                'quantity'       => $qty,
                'subtotal'       => $subtotal,
            ];
        }

        // ── 3. Persist atomically ─────────────────────────────────────────────
        $order = DB::transaction(function () use ($data, $total, $lineItems): Order {
            $order = Order::create([
                'code'           => '',   // auto-generated in Order::booted()
                'customer_name'  => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'customer_email' => $data['customer_email'] ?? null,
                'delivery_type'  => $data['delivery_type'],
                'address'        => $data['address'] ?? null,
                'notes'          => $data['notes'] ?? null,
                'status'         => Order::STATUS_PENDING,
                'total'          => round($total, 2),
            ]);

            foreach ($lineItems as $line) {
                OrderItem::create(array_merge($line, ['order_id' => $order->id]));
            }

            return $order;
        });

        // ── 4. Dispatch async notification (non-blocking) ─────────────────────
        NotifyNewOrderJob::dispatch($order);

        return $order->load('items');
    }
}
