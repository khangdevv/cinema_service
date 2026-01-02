<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Quiz\Account;
use Illuminate\Support\Facades\Hash;

// Create test account
$account = Account::firstOrCreate(
    ['email' => 'student@test.com'],
    [
        'full_name' => 'Sinh Viên Test',
        'password' => Hash::make('123456'),
        'role' => 'USER',
        'is_active' => true
    ]
);

echo "========================================\n";
echo "THÔNG TIN ĐĂNG NHẬP\n";
echo "========================================\n";
echo "Email: student@test.com\n";
echo "Password: 123456\n";
echo "========================================\n";
echo "\nTruy cập: http://localhost:5173/quiz/login\n";
