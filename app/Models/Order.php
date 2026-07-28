<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    // ── Status constants ───────────────────────────────────────────────────────

    const STATUS_PENDING          = 'pending';
    const STATUS_PREPARING        = 'preparing';
    const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';
    const STATUS_COMPLETED        = 'completed';
    const STATUS_CANCELED         = 'canceled';

    /** Ordered workflow steps (excluding canceled, which is a side-exit). */
    const WORKFLOW = [
        self::STATUS_PENDING,
        self::STATUS_PREPARING,
        self::STATUS_OUT_FOR_DELIVERY,
        self::STATUS_COMPLETED,
    ];

    /** Human-readable PT-BR labels for each status. */
    const STATUS_LABELS = [
        self::STATUS_PENDING          => 'Pendente',
        self::STATUS_PREPARING        => 'Preparando',
        self::STATUS_OUT_FOR_DELIVERY => 'Saiu para entrega',
        self::STATUS_COMPLETED        => 'Concluído',
        self::STATUS_CANCELED         => 'Cancelado',
    ];

    // ── Fillable ───────────────────────────────────────────────────────────────

    protected $fillable = [
        'code',
        'customer_name',
        'customer_phone',
        'customer_email',
        'delivery_type',
        'address',
        'notes',
        'status',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'total'      => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // ── Boot — auto-generate order code ───────────────────────────────────────

    protected static function booted(): void
    {
        static::created(function (Order $order): void {
            if (empty($order->code)) {
                $order->updateQuietly([
                    'code' => 'NNU-' . str_pad((string) $order->id, 5, '0', STR_PAD_LEFT),
                ]);
            }
        });
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    public function isDelivery(): bool
    {
        return $this->delivery_type === 'delivery';
    }
}
