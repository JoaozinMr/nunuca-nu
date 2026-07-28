<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:120'],
            'flavor'      => ['required', 'string', 'max:120'],
            'category'    => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price'       => ['required', 'numeric', 'min:0.01', 'max:9999.99'],
            'stock'       => ['required', 'integer', 'min:0'],
            'min_stock'   => ['required', 'integer', 'min:0'],
            'is_available'=> ['boolean'],
            'is_new'      => ['boolean'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Informe o nome do produto.',
            'flavor.required'   => 'Informe o sabor ou variação.',
            'category.required' => 'Informe a categoria.',
            'price.required'    => 'Informe o preço.',
            'price.min'         => 'O preço deve ser maior que zero.',
            'stock.required'    => 'Informe o estoque.',
            'min_stock.required'=> 'Informe o estoque mínimo.',
            'image.image'       => 'O arquivo deve ser uma imagem.',
            'image.max'         => 'A imagem deve ter no máximo 4 MB.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Coerce checkbox booleans sent as "1"/"0" strings from FormData
        $this->merge([
            'is_available' => filter_var($this->is_available ?? true,  FILTER_VALIDATE_BOOLEAN),
            'is_new'       => filter_var($this->is_new       ?? false, FILTER_VALIDATE_BOOLEAN),
        ]);
    }
}
