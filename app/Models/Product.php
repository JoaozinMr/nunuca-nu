<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'flavor',
        'category',
        'description',
        'price',
        'image_path',
        'is_available',
        'is_new',
        'stock',
        'min_stock',
    ];

    protected function casts(): array
    {
        return [
            'price'        => 'decimal:2',
            'is_available' => 'boolean',
            'is_new'       => 'boolean',
            'stock'        => 'integer',
            'min_stock'    => 'integer',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_available', true);
    }

    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereColumn('stock', '<=', 'min_stock');
    }

    // ── Computed ───────────────────────────────────────────────────────────────

    public function isLowStock(): bool
    {
        return $this->stock <= $this->min_stock;
    }

    /** Returns the public URL for the product image, or null. */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path
            ? asset('storage/' . $this->image_path)
            : null;
    }
}
