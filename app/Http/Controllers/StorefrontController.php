<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class StorefrontController extends Controller
{
    public function index(): Response
    {
        $products = Product::available()
            ->orderByDesc('is_new')
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return Inertia::render('Home', [
            'products' => ProductResource::collection($products),
            'pix'      => [
                'key'         => config('app.pix_key', env('PIX_KEY', '')),
                'beneficiary' => config('app.pix_beneficiary', env('PIX_BENEFICIARY', 'nunuca.nu')),
            ],
        ]);
    }
}
