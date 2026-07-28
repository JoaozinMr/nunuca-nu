<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            // Image is optional on update — only validated if provided
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    public function messages(): array
    {
        return (new StoreProductRequest())->messages();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_available' => filter_var($this->is_available ?? true,  FILTER_VALIDATE_BOOLEAN),
            'is_new'       => filter_var($this->is_new       ?? false, FILTER_VALIDATE_BOOLEAN),
        ]);
    }
}
