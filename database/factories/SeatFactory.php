<?php

namespace Database\Factories;

use App\Models\Screen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Seat>
 */
class SeatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'screen_id' => Screen::inRandomOrder()->first()?->id ?? 1,
            'row_label' => fake()->randomElement(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H']),
            'seat_number' => fake()->numberBetween(1, 15),
            'seat_type' => fake()->randomElement(['STANDARD', 'VIP', 'COUPLE', 'ACCESSIBLE']),
            'is_aisle' => fake()->boolean(10),
            'is_blocked' => fake()->boolean(5),
        ];
    }
}
