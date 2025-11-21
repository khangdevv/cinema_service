<?php

namespace Database\Seeders;

use App\Models\Screen;
use App\Models\Seat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
