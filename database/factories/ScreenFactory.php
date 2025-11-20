<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Screen>
 */
class ScreenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $count = 0;
        $count++;
        
        return [
            'code' => 'SCR-' . $count,
            'name' => 'Screen ' . $count,
            'format' => fake()->randomElement(['2D', '3D', 'IMAX', '4DX', 'ScreenX']),
            'row_count' => fake()->numberBetween(8, 15),
            'col_count' => fake()->numberBetween(8, 15),
            'is_active' => fake()->boolean(90),
        ];
    }
}
