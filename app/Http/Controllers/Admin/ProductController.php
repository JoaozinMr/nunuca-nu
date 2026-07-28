<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(): Response
    {
        $products = Product::orderBy('category')->orderBy('name')->get();

        return Inertia::render('Admin/Products/Index', [
            'products'     => ProductResource::collection($products),
            'lowStockCount'=> Product::lowStock()->count(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')
                ->store('products', 'public');
        }

        unset($data['image']);

        Product::create($data);

        return back()->with('success', 'Produto criado com sucesso!');
    }

    /**
     * Update an existing product.
     *
     * File uploads can't use PUT/PATCH directly in HTML forms, so this route
     * accepts POST with an `_method=PATCH` field (Inertia method spoofing).
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            // Delete old image to avoid orphaned files
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }

            $data['image_path'] = $request->file('image')
                ->store('products', 'public');
        }

        unset($data['image']);

        $product->update($data);

        return back()->with('success', 'Produto atualizado com sucesso!');
    }

    public function destroy(Product $product): RedirectResponse
    {
        // Remove image from disk before deleting the record
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return back()->with('success', 'Produto removido.');
    }
}
