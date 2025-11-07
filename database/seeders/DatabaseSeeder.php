<?php

namespace Database\Seeders;

use App\Models\Account;
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
    }
}
