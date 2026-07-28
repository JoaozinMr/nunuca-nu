<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Storefront is public
    }

    public function rules(): array
    {
        return [
            // Customer info
            'customer_name'  => ['required', 'string', 'max:120'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'customer_email' => ['nullable', 'email', 'max:120'],
            'notes'          => ['nullable', 'string', 'max:500'],

            // Delivery
            'delivery_type'  => ['required', 'in:delivery,pickup'],
            'address'        => ['nullable', 'string', 'max:255',
                // Required only when delivery_type is 'delivery'
                'required_if:delivery_type,delivery',
            ],

            // Cart items
            'items'             => ['required', 'array', 'min:1'],
            'items.*.product_id'=> ['required', 'integer', 'exists:products,id'],
            'items.*.quantity'  => ['required', 'integer', 'min:1', 'max:99'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required'       => 'Informe seu nome.',
            'customer_phone.required'      => 'Informe seu telefone.',
            'customer_email.email'         => 'E-mail inválido.',
            'delivery_type.required'       => 'Selecione a forma de entrega.',
            'delivery_type.in'             => 'Forma de entrega inválida.',
            'address.required_if'          => 'Informe o endereço para entrega.',
            'items.required'               => 'Seu carrinho está vazio.',
            'items.min'                    => 'Adicione pelo menos um produto.',
            'items.*.product_id.exists'    => 'Produto não encontrado.',
            'items.*.quantity.min'         => 'A quantidade mínima é 1.',
            'items.*.quantity.max'         => 'A quantidade máxima por item é 99.',
        ];
    }

    /** Normalize phone: strip everything except digits, +, (, ), - and space. */
    protected function prepareForValidation(): void
    {
        if ($this->has('customer_name')) {
            $this->merge(['customer_name' => trim($this->customer_name)]);
        }
    }
}
