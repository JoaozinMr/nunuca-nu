<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Product */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'flavor'      => $this->flavor,
            'category'    => $this->category,
            'description' => $this->description,
            'price'       => (float) $this->price,
            'image_url'   => $this->image_url, // via accessor on model
            'is_available'=> $this->is_available,
            'is_new'      => $this->is_new,
            'stock'       => $this->stock,
            'min_stock'   => $this->min_stock,
            'is_low_stock'=> $this->isLowStock(),
            'created_at'  => $this->created_at->format('d/m/Y'),
        ];
    }
}
