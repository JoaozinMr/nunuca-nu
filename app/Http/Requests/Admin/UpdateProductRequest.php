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
            'image'       => ['nullable'], // Base64 or UploadedFile
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

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (request()->hasFile('image') && !request()->file('image')->isValid()) {
                \Log::error('Upload error code: ' . request()->file('image')->getError());
                \Log::error('Upload error message: ' . request()->file('image')->getErrorMessage());
            }
        });
    }
}
