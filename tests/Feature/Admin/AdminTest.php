<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    // ── Auth ──────────────────────────────────────────────────────────────────

    public function test_login_page_renders(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertInertia(fn($p) => $p->component('Admin/Login'));
    }

    public function test_admin_can_login(): void
    {
        $admin = $this->admin();

        $this->post('/admin/login', [
            'email'    => $admin->email,
            'password' => 'password',
        ])->assertRedirect('/admin/dashboard');
    }

    public function test_wrong_password_is_rejected(): void
    {
        $admin = $this->admin();

        $this->post('/admin/login', [
            'email'    => $admin->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');
    }

    public function test_unauthenticated_cannot_access_dashboard(): void
    {
        $this->get('/admin/dashboard')
            ->assertRedirect('/admin/login');
    }

    // ── Dashboard ─────────────────────────────────────────────────────────────

    public function test_dashboard_renders_with_stats(): void
    {
        Order::factory(3)->create(['status' => Order::STATUS_PENDING, 'total' => 20.00]);

        $this->actingAs($this->admin())
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertInertia(fn($p) => $p
                ->component('Admin/Dashboard')
                ->has('stats')
                ->has('recentOrders')
                ->where('stats.pending_count', 3)
            );
    }

    // ── Orders ────────────────────────────────────────────────────────────────

    public function test_orders_index_renders(): void
    {
        Order::factory(5)->create();

        $this->actingAs($this->admin())
            ->get('/admin/orders')
            ->assertOk()
            ->assertInertia(fn($p) => $p
                ->component('Admin/Orders/Index')
                ->has('orders')
                ->has('counts')
            );
    }

    public function test_order_status_can_be_updated(): void
    {
        $order = Order::factory()->create(['status' => Order::STATUS_PENDING, 'total' => 18.90]);

        $this->actingAs($this->admin())
            ->patch("/admin/orders/{$order->id}/status", ['status' => Order::STATUS_PREPARING])
            ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'id'     => $order->id,
            'status' => Order::STATUS_PREPARING,
        ]);
    }

    public function test_invalid_status_is_rejected(): void
    {
        $order = Order::factory()->create(['status' => Order::STATUS_PENDING, 'total' => 18.90]);

        $this->actingAs($this->admin())
            ->patch("/admin/orders/{$order->id}/status", ['status' => 'invalid_status'])
            ->assertSessionHasErrors('status');
    }

    // ── Products ─────────────────────────────────────────────────────────────

    public function test_products_index_renders(): void
    {
        Product::factory(4)->create();

        $this->actingAs($this->admin())
            ->get('/admin/products')
            ->assertOk()
            ->assertInertia(fn($p) => $p
                ->component('Admin/Products/Index')
                ->has('products', 4)
            );
    }

    public function test_admin_can_create_product(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->post('/admin/products', [
                'name'        => 'Brigadeiro',
                'flavor'      => 'tradicional',
                'category'    => 'Brigadeiro',
                'description' => 'Clássico brigadeiro artesanal.',
                'price'       => '5.50',
                'stock'       => '30',
                'min_stock'   => '5',
                'is_available'=> '1',
                'is_new'      => '0',
                'image'       => UploadedFile::fake()->image('brigadeiro.jpg', 800, 800),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('products', ['name' => 'Brigadeiro', 'flavor' => 'tradicional']);

        $product = Product::where('name', 'Brigadeiro')->first();
        $this->assertNotNull($product->image_path);
        Storage::disk('public')->assertExists($product->image_path);
    }

    public function test_admin_can_delete_product(): void
    {
        $product = Product::factory()->create();

        $this->actingAs($this->admin())
            ->delete("/admin/products/{$product->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_product_creation_requires_name_flavor_price(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/products', ['stock' => 10, 'min_stock' => 2])
            ->assertSessionHasErrors(['name', 'flavor', 'price', 'category']);
    }
}
