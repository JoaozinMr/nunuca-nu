<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * The four real products from the nunuca mockup, used by the seeder.
     * Paths are relative to storage/app/public/products/.
     */
    public static array $catalogProducts = [
        [
            'name'        => 'Copo',
            'flavor'      => 'brigadeiro',
            'category'    => 'Copinho',
            'description' => 'Camadas de brigadeiro cremoso, farofa crocante e calda especial.',
            'price'       => 18.90,
            'image_path'  => 'products/copo-brigadeiro.jpg',
            'is_available'=> true,
            'is_new'      => false,
            'stock'       => 30,
            'min_stock'   => 5,
        ],
        [
            'name'        => 'Alfajor',
            'flavor'      => 'doce de leite',
            'category'    => 'Alfajor',
            'description' => '3 camadas de biscoito, 2 de doce de leite cremoso, coberto por chocolate blend.',
            'price'       => 14.90,
            'image_path'  => 'products/alfajor-doce-de-leite.jpg',
            'is_available'=> true,
            'is_new'      => false,
            'stock'       => 20,
            'min_stock'   => 5,
        ],
        [
            'name'        => 'Palha Italiana',
            'flavor'      => 'brigadeiro',
            'category'    => 'Palha Italiana',
            'description' => 'Palha italiana com recheio generoso de brigadeiro artesanal.',
            'price'       => 9.50,
            'image_path'  => 'products/palha-brigadeiro.jpg',
            'is_available'=> true,
            'is_new'      => false,
            'stock'       => 40,
            'min_stock'   => 10,
        ],
        [
            'name'        => 'Palha Italiana',
            'flavor'      => 'oreo',
            'category'    => 'Palha Italiana',
            'description' => 'Palha italiana com recheio de chocolate branco e pedaços de Oreo.',
            'price'       => 9.50,
            'image_path'  => 'products/palha-oreo.jpg',
            'is_available'=> true,
            'is_new'      => true,
            'stock'       => 25,
            'min_stock'   => 10,
        ],
    ];

    public function definition(): array
    {
        $categories = ['Copinho', 'Alfajor', 'Palha Italiana', 'Trufas'];
        $flavors    = ['brigadeiro', 'doce de leite', 'oreo', 'morango', 'limão'];

        return [
            'name'        => fake()->randomElement(['Copo', 'Alfajor', 'Palha Italiana', 'Trufa']),
            'flavor'      => fake()->randomElement($flavors),
            'category'    => fake()->randomElement($categories),
            'description' => fake()->sentence(10),
            'price'       => fake()->randomFloat(2, 8.00, 35.00),
            'image_path'  => null,
            'is_available'=> fake()->boolean(80),
            'is_new'      => fake()->boolean(20),
            'stock'       => fake()->numberBetween(0, 50),
            'min_stock'   => 5,
        ];
    }
}
