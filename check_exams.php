<?php

use Illuminate\Support\Facades\DB;

echo "=== QUIZ EXAMS ===" . PHP_EOL;
$exams = DB::table('quiz_exams')->select('id', 'name', 'slug')->get();
foreach ($exams as $exam) {
    echo "ID: {$exam->id} - {$exam->name} ({$exam->slug})" . PHP_EOL;
}

echo PHP_EOL . "=== QUESTIONS COUNT PER EXAM ===" . PHP_EOL;
$counts = DB::table('quiz_questions')
    ->select('exam_id', DB::raw('COUNT(*) as count'))
    ->groupBy('exam_id')
    ->get();

foreach ($counts as $count) {
    $examName = DB::table('quiz_exams')->where('id', $count->exam_id)->value('name');
    echo "{$examName}: {$count->count} câu" . PHP_EOL;
}

echo PHP_EOL . "=== EXAMS WITHOUT QUESTIONS ===" . PHP_EOL;
$examIds = $counts->pluck('exam_id')->toArray();
$emptyExams = DB::table('quiz_exams')
    ->whereNotIn('id', $examIds)
    ->select('id', 'name')
    ->get();

if ($emptyExams->isEmpty()) {
    echo "Tất cả đề đều có câu hỏi!" . PHP_EOL;
} else {
    foreach ($emptyExams as $exam) {
        echo "❌ {$exam->name} (ID: {$exam->id}) - 0 câu" . PHP_EOL;
    }
}
