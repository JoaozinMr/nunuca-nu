<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Order */
class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'code'           => $this->code,
            'customer_name'  => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'customer_email' => $this->customer_email,
            'delivery_type'  => $this->delivery_type,
            'address'        => $this->address,
            'notes'          => $this->notes,
            'status'         => $this->status,
            'status_label'   => $this->status_label,          // PT-BR label from model accessor
            'total'          => (float) $this->total,
            'total_items'    => $this->total_items,           // sum of quantities
            'items'          => OrderItemResource::collection($this->whenLoaded('items')),
            'created_at'     => $this->created_at->format('d/m/Y H:i'),
            'created_at_iso' => $this->created_at->toIso8601String(),
            // Status workflow helpers for the Vue frontend
            'workflow'       => Order::WORKFLOW,
            'status_labels'  => Order::STATUS_LABELS,
        ];
    }
}
