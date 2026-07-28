<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();

        if ($products->isEmpty()) {
            $this->command->warn('No products found. Run ProductSeeder first.');
            return;
        }

        // Create 15 realistic sample orders
        Order::factory(15)->create()->each(function (Order $order) use ($products): void {
            $itemCount = random_int(1, 3);
            $total     = 0.00;

            $pickedProducts = $products->random(min($itemCount, $products->count()));

            foreach ($pickedProducts as $product) {
                $qty      = random_int(1, 4);
                $subtotal = round((float) $product->price * $qty, 2);
                $total   += $subtotal;

                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $product->id,
                    'product_name'  => $product->name,
                    'product_flavor'=> $product->flavor,
                    'unit_price'    => $product->price,
                    'quantity'      => $qty,
                    'subtotal'      => $subtotal,
                ]);
            }

            $order->updateQuietly(['total' => round($total, 2)]);
        });
    }
}
