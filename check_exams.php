<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== All Exams ===\n";
$exams = DB::connection('tracnghiem')->table('quiz_exams')->get();
foreach ($exams as $exam) {
    $questionCount = DB::connection('tracnghiem')
        ->table('quiz_questions')
        ->where('exam_id', $exam->id)
        ->count();
    echo "Exam ID: {$exam->id}, Name: {$exam->name}, Questions: {$questionCount}\n";
}

echo "\n=== Questions per exam_id ===\n";
$grouped = DB::connection('tracnghiem')
    ->table('quiz_questions')
    ->select('exam_id', DB::raw('count(*) as count'))
    ->groupBy('exam_id')
    ->get();
foreach ($grouped as $row) {
    echo "exam_id: {$row->exam_id}, count: {$row->count}\n";
}

echo "\n=== Total questions ===\n";
echo DB::connection('tracnghiem')->table('quiz_questions')->count() . "\n";
