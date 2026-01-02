<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

$report = "QUIZ DATA VALIDATION REPORT\n";
$report .= "Generated: " . now()->format('Y-m-d H:i:s') . "\n";
$report .= str_repeat('=', 80) . "\n\n";

$exams = DB::table('quiz_exams')->select('id', 'name')->get();
$totalIssues = 0;
$allIssues = [];

foreach ($exams as $exam) {
    $report .= "EXAM: {$exam->name}\n";
    $report .= str_repeat('-', 80) . "\n";

    $questions = DB::table('quiz_questions')
        ->where('exam_id', $exam->id)
        ->get();

    $report .= "Total questions: " . $questions->count() . "\n\n";

    if ($questions->isEmpty()) {
        $report .= "ERROR: NO QUESTIONS FOUND!\n\n";
        continue;
    }

    $examIssues = [];

    foreach ($questions as $q) {
        $questionIssues = [];

        if (empty(trim($q->question))) {
            $questionIssues[] = "Missing question text";
        }
        if (empty(trim($q->option_a)))
            $questionIssues[] = "Missing option A";
        if (empty(trim($q->option_b)))
            $questionIssues[] = "Missing option B";
        if (empty(trim($q->option_c)))
            $questionIssues[] = "Missing option C";
        if (empty(trim($q->option_d)))
            $questionIssues[] = "Missing option D";

        $answer = strtoupper(trim($q->correct_answer));
        if (!in_array($answer, ['A', 'B', 'C', 'D'])) {
            $questionIssues[] = "Invalid answer: '{$q->correct_answer}'";
        }

        if (empty(trim($q->explanation))) {
            $questionIssues[] = "Missing explanation";
        }

        if (!empty($questionIssues)) {
            $examIssues[] = [
                'id' => $q->id,
                'question' => substr($q->question, 0, 80),
                'issues' => $questionIssues
            ];
            $totalIssues += count($questionIssues);
        }
    }

    if (empty($examIssues)) {
        $report .= "STATUS: OK - All questions are valid!\n\n";
    } else {
        $report .= "STATUS: ISSUES FOUND - " . count($examIssues) . " questions have problems\n\n";

        // Show first 10 issues
        foreach (array_slice($examIssues, 0, 10) as $issue) {
            $report .= "  Question ID {$issue['id']}:\n";
            $report .= "    Text: {$issue['question']}...\n";
            foreach ($issue['issues'] as $iss) {
                $report .= "    - {$iss}\n";
            }
            $report .= "\n";
        }

        if (count($examIssues) > 10) {
            $remaining = count($examIssues) - 10;
            $report .= "  ... and {$remaining} more questions with issues\n\n";
        }
    }

    $allIssues[$exam->name] = $examIssues;
}

$report .= str_repeat('=', 80) . "\n";
$report .= "SUMMARY\n";
$report .= str_repeat('=', 80) . "\n";
$report .= "Total issues: {$totalIssues}\n";

if ($totalIssues === 0) {
    $report .= "STATUS: ALL DATA IS VALID!\n";
} else {
    $report .= "STATUS: ISSUES NEED TO BE FIXED!\n";
    $report .= "\nBreakdown by exam:\n";
    foreach ($allIssues as $examName => $issues) {
        $count = count($issues);
        $report .= "  - {$examName}: {$count} questions with issues\n";
    }
}

// Save to file
$filePath = base_path('quiz_validation_report.txt');
File::put($filePath, $report);

echo $report;
echo "\nReport saved to: {$filePath}\n";
