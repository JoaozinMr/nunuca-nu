<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Wraps the Telegram Bot API for sending order notifications and daily reports.
 *
 * All public methods return bool — they are designed to be called from
 * queued jobs and must NOT throw exceptions (failures are logged instead).
 */
class TelegramService
{
    private const API_BASE = 'https://api.telegram.org/bot';
    private const TIMEOUT  = 10; // seconds

    public function __construct(
        private readonly string $botToken,
        private readonly string $chatId,
    ) {}

    // ── Public API ─────────────────────────────────────────────────────────────

    /**
     * Send a plain-text or Markdown message to the configured chat.
     */
    public function sendMessage(string $text, string $parseMode = 'Markdown'): bool
    {
        return $this->post('sendMessage', [
            'chat_id'                  => $this->chatId,
            'text'                     => $text,
            'parse_mode'               => $parseMode,
            'disable_web_page_preview' => true,
        ]);
    }

    /**
     * Build and send a rich new-order notification.
     */
    public function notifyNewOrder(Order $order): bool
    {
        $order->loadMissing('items');

        $itemLines = $order->items->map(fn($item) =>
            "  • {$item->product_name} ({$item->product_flavor}) × {$item->quantity} — " .
            $this->brl($item->subtotal)
        )->implode("\n");

        $deliveryLine = $order->isDelivery()
            ? "🚚 *Entrega* — {$order->address}"
            : '🏪 *Retirada no local*';

        $message = implode("\n", [
            "🍬 *Novo pedido recebido!*",
            "",
            "📋 *Pedido:* `{$order->code}`",
            "👤 *Cliente:* {$order->customer_name}",
            "📞 *Telefone:* {$order->customer_phone}",
            $deliveryLine,
            "",
            "*Itens:*",
            $itemLines,
            "",
            "💰 *Total: " . $this->brl($order->total) . "*",
            $order->notes ? "\n📝 *Obs:* {$order->notes}" : '',
            "",
            "⏰ " . now()->format('d/m/Y H:i'),
        ]);

        return $this->sendMessage(trim($message));
    }

    /**
     * Compile daily stats and send a summary report.
     *
     * @param array{
     *   date: string,
     *   orders_today: int,
     *   revenue_today: float,
     *   orders_total: int,
     *   revenue_total: float,
     *   pending: int,
     *   preparing: int,
     *   out_for_delivery: int,
     *   completed_today: int,
     *   low_stock: array<string>
     * } $stats
     */
    public function sendDailyReport(array $stats): bool
    {
        $lowStockLines = empty($stats['low_stock'])
            ? '  ✅ Todos os produtos com estoque adequado'
            : collect($stats['low_stock'])->map(fn($p) => "  ⚠️ {$p}")->implode("\n");

        $message = implode("\n", [
            "📊 *Relatório Diário — nunuca.nu*",
            "📅 {$stats['date']}",
            "",
            "*Hoje:*",
            "  🛍️ Pedidos: {$stats['orders_today']}",
            "  💵 Receita: " . $this->brl($stats['revenue_today']),
            "  ✅ Concluídos: {$stats['completed_today']}",
            "",
            "*Status em aberto:*",
            "  🕐 Pendentes: {$stats['pending']}",
            "  🍫 Preparando: {$stats['preparing']}",
            "  🚚 Em entrega: {$stats['out_for_delivery']}",
            "",
            "*Acumulado total:*",
            "  📦 Pedidos: {$stats['orders_total']}",
            "  💰 Receita: " . $this->brl($stats['revenue_total']),
            "",
            "*Estoque baixo:*",
            $lowStockLines,
        ]);

        return $this->sendMessage(trim($message));
    }

    // ── Private helpers ────────────────────────────────────────────────────────

    private function post(string $method, array $payload): bool
    {
        if (empty($this->botToken) || $this->botToken === 'your-bot-token-here') {
            Log::info('TelegramService: bot token not configured, skipping.', [
                'method'  => $method,
                'payload' => $payload,
            ]);
            return false;
        }

        try {
            $response = Http::timeout(self::TIMEOUT)
                ->post(self::API_BASE . $this->botToken . '/' . $method, $payload);

            if (! $response->successful()) {
                Log::warning('TelegramService: API error', [
                    'method'  => $method,
                    'status'  => $response->status(),
                    'body'    => $response->body(),
                ]);
                return false;
            }

            return (bool) ($response->json('ok') ?? false);
        } catch (\Throwable $e) {
            // Re-throw so the queued job can handle retries correctly
            throw $e;
        }
    }

    /** Format a float as Brazilian Real currency string. */
    private function brl(float|string $value): string
    {
        return 'R$ ' . number_format((float) $value, 2, ',', '.');
    }
}
