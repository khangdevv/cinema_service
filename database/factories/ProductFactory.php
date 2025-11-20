<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $products = [
            ['name' => 'Popcorn', 'price' => 50000],
            ['name' => 'Coca Cola', 'price' => 30000],
            ['name' => 'Nước Suối', 'price' => 15000],
            ['name' => 'Hotdog', 'price' => 35000],
            ['name' => 'Combo Popcorn + Nước', 'price' => 70000],
        ];

        $product = fake()->randomElement($products);

        return [
            'name' => $product['name'],
            'price' => $product['price'],
            'is_active' => fake()->boolean(90),
        ];
    }
}
