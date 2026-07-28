<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Jobs\NotifyNewOrderJob;
use App\Jobs\SendDailyReportJob;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\TelegramService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TelegramServiceTest extends TestCase
{
    use RefreshDatabase;

    // ── TelegramService unit tests ─────────────────────────────────────────────

    public function test_send_message_returns_false_when_token_not_configured(): void
    {
        $service = new TelegramService(botToken: '', chatId: '123');

        Log::shouldReceive('info')->once();

        $result = $service->sendMessage('test');

        $this->assertFalse($result);
    }

    public function test_send_message_returns_false_when_placeholder_token(): void
    {
        $service = new TelegramService(botToken: 'your-bot-token-here', chatId: '123');

        Log::shouldReceive('info')->once();

        $result = $service->sendMessage('test');

        $this->assertFalse($result);
    }

    public function test_send_message_calls_telegram_api(): void
    {
        Http::fake([
            'api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);

        $service = new TelegramService(botToken: 'real-token-123', chatId: '456789');

        $result = $service->sendMessage('Hello from nunuca.nu!');

        $this->assertTrue($result);
        Http::assertSent(fn($req) => str_contains($req->url(), 'sendMessage'));
    }

    public function test_notify_new_order_sends_correct_message(): void
    {
        Http::fake([
            'api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);

        $product = Product::factory()->create([
            'name'  => 'Alfajor',
            'flavor'=> 'doce de leite',
            'price' => 14.90,
        ]);

        $order = Order::factory()->create([
            'customer_name'  => 'João Silva',
            'customer_phone' => '(11) 99999-1234',
            'delivery_type'  => 'delivery',
            'address'        => 'Rua das Flores, 123',
            'status'         => Order::STATUS_PENDING,
            'total'          => 29.80,
        ]);

        OrderItem::create([
            'order_id'       => $order->id,
            'product_id'     => $product->id,
            'product_name'   => $product->name,
            'product_flavor' => $product->flavor,
            'unit_price'     => $product->price,
            'quantity'       => 2,
            'subtotal'       => 29.80,
        ]);

        $service = new TelegramService(botToken: 'real-token-123', chatId: '456789');

        $result = $service->notifyNewOrder($order);

        $this->assertTrue($result);

        Http::assertSent(function ($request) use ($order) {
            $body = $request->data();
            return isset($body['text'])
                && str_contains($body['text'], $order->code)
                && str_contains($body['text'], 'João Silva')
                && str_contains($body['text'], 'Alfajor');
        });
    }

    // ── Job dispatch tests ─────────────────────────────────────────────────────

    public function test_notify_new_order_job_is_dispatched_to_queue(): void
    {
        Queue::fake();

        $order = Order::factory()->create(['total' => 18.90]);

        NotifyNewOrderJob::dispatch($order);

        Queue::assertPushed(NotifyNewOrderJob::class, function ($job) use ($order) {
            return true; // job was pushed
        });
    }

    public function test_notify_new_order_job_has_correct_retry_config(): void
    {
        $order = Order::factory()->create(['total' => 18.90]);
        $job   = new NotifyNewOrderJob($order);

        $this->assertSame(5, $job->tries);
        $this->assertSame([10, 60, 300], $job->backoff);
        $this->assertSame(30, $job->timeout);
    }

    public function test_send_daily_report_job_has_correct_retry_config(): void
    {
        $job = new SendDailyReportJob();

        $this->assertSame(3, $job->tries);
        $this->assertSame([30, 120], $job->backoff);
    }

    public function test_send_daily_report_job_is_dispatched_to_queue(): void
    {
        Queue::fake();

        SendDailyReportJob::dispatch();

        Queue::assertPushed(SendDailyReportJob::class);
    }

    public function test_daily_report_job_handles_missing_token_gracefully(): void
    {
        // Seed some data
        Product::factory()->create(['stock' => 2, 'min_stock' => 5]);
        Order::factory()->create(['status' => Order::STATUS_PENDING, 'total' => 18.90]);

        // With no token, sendDailyReport logs and returns false — job should not throw
        Log::shouldReceive('info')->atLeast()->once();
        Log::shouldReceive('error')->never();

        $service = new TelegramService(botToken: '', chatId: '');
        $job     = new SendDailyReportJob();

        $threw = false;
        try {
            $job->handle($service);
        } catch (\Throwable $e) {
            $threw = true;
        }

        $this->assertFalse($threw, 'Job should not throw when Telegram token is not configured.');
    }
}
