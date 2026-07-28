<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Order;
use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Notifies the store owner via Telegram when a new order is placed.
 *
 * This job is deliberately decoupled from the checkout request — it runs
 * asynchronously so that a Telegram API failure never affects the customer.
 *
 * Retry policy:
 *   - 5 attempts total
 *   - Exponential backoff: 10s → 60s → 300s → 300s → 300s
 *   - Failures beyond $tries are logged in the failed_jobs table
 */
class NotifyNewOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Maximum number of attempts before the job is considered failed. */
    public int $tries = 5;

    /**
     * Backoff in seconds between retries (exponential curve).
     * Laravel will cycle through these values; the last one repeats.
     *
     * @var int[]
     */
    public array $backoff = [10, 60, 300];

    /** Timeout for a single attempt in seconds. */
    public int $timeout = 30;

    public function __construct(
        private readonly Order $order,
    ) {}

    public function handle(TelegramService $telegram): void
    {
        $this->order->loadMissing('items');

        $sent = $telegram->notifyNewOrder($this->order);

        if (! $sent) {
            Log::info('NotifyNewOrderJob: notification skipped (token not configured).', [
                'order_code' => $this->order->code,
            ]);
        } else {
            Log::info('NotifyNewOrderJob: Telegram notification sent.', [
                'order_code' => $this->order->code,
                'attempt'    => $this->attempts(),
            ]);
        }
    }

    /**
     * Handle a job failure after all retries are exhausted.
     * The failed_jobs table will contain the full serialized payload
     * for manual reprocessing via `php artisan queue:retry`.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('NotifyNewOrderJob: all retries exhausted.', [
            'order_code' => $this->order->code,
            'error'      => $exception->getMessage(),
        ]);
    }
}
