<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['name' => 'Popcorn', 'price' => 50000, 'is_active' => true],
            ['name' => 'Coca Cola', 'price' => 30000, 'is_active' => true],
            ['name' => 'Nước Suối', 'price' => 15000, 'is_active' => true],
            ['name' => 'Hotdog', 'price' => 35000, 'is_active' => true],
            ['name' => 'Combo Popcorn + Nước', 'price' => 70000, 'is_active' => true],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
