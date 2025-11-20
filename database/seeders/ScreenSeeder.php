<?php

namespace Database\Seeders;

use App\Models\Screen;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScreenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Screen::factory()->count(8)->create();
    }
}
