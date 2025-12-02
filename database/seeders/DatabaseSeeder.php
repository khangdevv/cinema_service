<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Product;
use App\Models\Movie;
use App\Models\Screen;
use App\Models\Seat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Account::create([
            'full_name' => 'Admin User',
            'email' => 'admin@cinema.com',
            'password_hash' => Hash::make('admin'),
            'phone' => '0123456789',
            'role' => 'ADMIN',
            'is_active' => true,
        ]);

        Account::factory(10)->create();

        Movie::factory()->count(30)->create();

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

        Screen::factory()->count(8)->create();

        $screens = Screen::all();

        foreach ($screens as $screen) {
            $rowLabels = range('A', 'Z');
            
            for ($row = 0; $row < $screen->row_count; $row++) {
                for ($col = 1; $col <= $screen->col_count; $col++) {
                    Seat::create([
                        'screen_id' => $screen->id,
                        'row_label' => $rowLabels[$row],
                        'seat_number' => $col,
                        'seat_type' => 'STANDARD',
                        'is_aisle' => false,
                        'is_blocked' => false,
                    ]);
                }
            }
        }
    }
}
