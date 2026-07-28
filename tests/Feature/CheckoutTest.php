<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function makeProducts(): array
    {
        return [
            Product::factory()->create(['name' => 'Alfajor', 'flavor' => 'doce de leite', 'price' => 14.90, 'is_available' => true]),
            Product::factory()->create(['name' => 'Copo',    'flavor' => 'brigadeiro',    'price' => 18.90, 'is_available' => true]),
        ];
    }

    public function test_storefront_returns_available_products(): void
    {
        $this->makeProducts();
        Product::factory()->create(['is_available' => false]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertInertia(fn($page) => $page
            ->component('Home')
            ->has('products', 2)
            ->has('pix')
        );
    }

    public function test_checkout_creates_order_and_redirects_to_confirmation(): void
    {
        Queue::fake();

        [$p1, $p2] = $this->makeProducts();

        $payload = [
            'customer_name'  => 'Maria Silva',
            'customer_phone' => '(11) 99999-0001',
            'customer_email' => 'maria@example.com',
            'delivery_type'  => 'delivery',
            'address'        => 'Rua das Flores, 42',
            'notes'          => '',
            'items'          => [
                ['product_id' => $p1->id, 'quantity' => 2],
                ['product_id' => $p2->id, 'quantity' => 1],
            ],
        ];

        $response = $this->post('/checkout', $payload);

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertStringStartsWith('NNU-', $order->code);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals(round(14.90 * 2 + 18.90, 2), (float) $order->total);
        $this->assertCount(2, $order->items);

        $response->assertRedirect("/pedido/{$order->code}");
    }

    public function test_checkout_rejects_empty_cart(): void
    {
        $response = $this->post('/checkout', [
            'customer_name'  => 'Test',
            'customer_phone' => '11999999999',
            'delivery_type'  => 'pickup',
            'items'          => [],
        ]);

        $response->assertSessionHasErrors('items');
    }

    public function test_checkout_requires_address_for_delivery(): void
    {
        $product = Product::factory()->create(['is_available' => true]);

        $response = $this->post('/checkout', [
            'customer_name'  => 'Test',
            'customer_phone' => '11999999999',
            'delivery_type'  => 'delivery',
            'address'        => '',          // missing!
            'items'          => [['product_id' => $product->id, 'quantity' => 1]],
        ]);

        $response->assertSessionHasErrors('address');
    }

    public function test_checkout_rejects_unavailable_products(): void
    {
        Queue::fake();

        $product = Product::factory()->create(['is_available' => false]);

        $response = $this->post('/checkout', [
            'customer_name'  => 'Test',
            'customer_phone' => '11999999999',
            'delivery_type'  => 'pickup',
            'items'          => [['product_id' => $product->id, 'quantity' => 1]],
        ]);

        $response->assertSessionHasErrors('items');
        $this->assertDatabaseEmpty('orders');
    }

    public function test_order_confirmation_page_renders(): void
    {
        $order = Order::factory()->create([
            'code'          => 'NNU-00001',
            'delivery_type' => 'pickup',
            'status'        => Order::STATUS_PENDING,
            'total'         => 18.90,
        ]);

        $product = Product::factory()->create();

        OrderItem::create([
            'order_id'       => $order->id,
            'product_id'     => $product->id,
            'product_name'   => 'Copo',
            'product_flavor' => 'brigadeiro',
            'unit_price'     => 18.90,
            'quantity'       => 1,
            'subtotal'       => 18.90,
        ]);

        $this->get("/pedido/{$order->code}")
            ->assertOk()
            ->assertInertia(fn($page) => $page
                ->component('OrderConfirmation')
                ->has('order')
                ->where('order.code', 'NNU-00001')
            );
    }
}
