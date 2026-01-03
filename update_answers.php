<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$examId = 2;

// Đáp án đúng theo thứ tự câu hỏi
$answers = [
    'b','d','b','b','a','d','d', // Chương 1: 7 câu
    'd','a', // Chương 2 lặp: 2 câu
    'b','a','a','d','a','a','a','a','a','a', // 10 câu
    'b','b','a','c','a','a','a','b','a','a', // 10 câu
    'a','b','a','a','a','a','a','a','a','d', // 10 câu
    'a','a','a','d','a','a','a','a','a','a', // 10 câu
    'c','a','a','a','a','a','a','a','a','a', // 10 câu
    'c','a','b','a','a','a','b','a','a','a', // 10 câu
    'a','a','d','a','a','a','a','a','d','c', // 10 câu
    'a','a','a','a','a','a','a','a','a','a','a', // 11 câu
    'a','a','a','a','a','a','d','c','d','a', // 10 câu
    'a','a','a','a','a','a','a','a','a','a', // 10 câu
    'a','b','a','a','a','a','a','a','a','a', // 10 câu
    'a','a','a','a', // 4 câu
];

// Lấy tất cả câu hỏi của exam_id = 2 theo ID tăng dần
$questions = DB::connection('tracnghiem')
    ->table('quiz_questions')
    ->where('exam_id', $examId)
    ->orderBy('id', 'asc')
    ->get();

echo "Tổng câu hỏi: " . count($questions) . "\n";
echo "Tổng đáp án: " . count($answers) . "\n\n";

$updated = 0;
foreach ($questions as $index => $question) {
    if (isset($answers[$index])) {
        DB::connection('tracnghiem')
            ->table('quiz_questions')
            ->where('id', $question->id)
            ->update(['correct_answer' => $answers[$index]]);
        $updated++;
        
        if ($index < 15) {
            echo "ID {$question->id}: đáp án = {$answers[$index]}\n";
        }
    }
}

echo "\n✅ Đã cập nhật đáp án cho {$updated} câu hỏi!\n";
