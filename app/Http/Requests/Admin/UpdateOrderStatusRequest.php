<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::in([
                    Order::STATUS_PENDING,
                    Order::STATUS_PREPARING,
                    Order::STATUS_OUT_FOR_DELIVERY,
                    Order::STATUS_COMPLETED,
                    Order::STATUS_CANCELED,
                ]),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Informe o status.',
            'status.in'       => 'Status inválido.',
        ];
    }
}
