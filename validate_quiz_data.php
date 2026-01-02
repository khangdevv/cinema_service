<?php

use Illuminate\Support\Facades\DB;

echo "🔍 VALIDATING QUIZ DATA QUALITY..." . PHP_EOL;
echo str_repeat('=', 70) . PHP_EOL . PHP_EOL;

$exams = DB::table('quiz_exams')->select('id', 'name')->get();
$totalIssues = 0;

foreach ($exams as $exam) {
    echo "📚 Exam: {$exam->name}" . PHP_EOL;
    echo str_repeat('-', 70) . PHP_EOL;

    $questions = DB::table('quiz_questions')
        ->where('exam_id', $exam->id)
        ->get();

    echo "   Total questions: " . $questions->count() . PHP_EOL;

    if ($questions->isEmpty()) {
        echo "   ❌ NO QUESTIONS FOUND!" . PHP_EOL . PHP_EOL;
        $totalIssues++;
        continue;
    }

    $issues = [
        'missing_question' => 0,
        'missing_option_a' => 0,
        'missing_option_b' => 0,
        'missing_option_c' => 0,
        'missing_option_d' => 0,
        'invalid_answer' => 0,
        'missing_explanation' => 0,
    ];

    $invalidAnswerQuestions = [];

    foreach ($questions as $q) {
        // Check question text
        if (empty(trim($q->question))) {
            $issues['missing_question']++;
        }

        // Check options
        if (empty(trim($q->option_a)))
            $issues['missing_option_a']++;
        if (empty(trim($q->option_b)))
            $issues['missing_option_b']++;
        if (empty(trim($q->option_c)))
            $issues['missing_option_c']++;
        if (empty(trim($q->option_d)))
            $issues['missing_option_d']++;

        // Check correct answer
        $answer = strtoupper(trim($q->correct_answer));
        if (!in_array($answer, ['A', 'B', 'C', 'D'])) {
            $issues['invalid_answer']++;
            $invalidAnswerQuestions[] = [
                'id' => $q->id,
                'question' => substr($q->question, 0, 60),
                'answer' => $q->correct_answer
            ];
        }

        // Check explanation
        if (empty(trim($q->explanation))) {
            $issues['missing_explanation']++;
        }
    }

    // Print issues
    $hasIssues = false;
    foreach ($issues as $type => $count) {
        if ($count > 0) {
            $hasIssues = true;
            $totalIssues += $count;

            $label = match ($type) {
                'missing_question' => 'Missing question text',
                'missing_option_a' => 'Missing option A',
                'missing_option_b' => 'Missing option B',
                'missing_option_c' => 'Missing option C',
                'missing_option_d' => 'Missing option D',
                'invalid_answer' => 'Invalid correct answer',
                'missing_explanation' => 'Missing explanation',
            };

            $icon = $type === 'missing_explanation' ? '⚠️' : '❌';
            echo "   {$icon} {$label}: {$count}" . PHP_EOL;
        }
    }

    if (!$hasIssues) {
        echo "   ✅ All good!" . PHP_EOL;
    }

    // Show details of invalid answers
    if (!empty($invalidAnswerQuestions)) {
        echo PHP_EOL . "   Invalid answer details:" . PHP_EOL;
        foreach (array_slice($invalidAnswerQuestions, 0, 5) as $iq) {
            echo "      - Q{$iq['id']}: '{$iq['answer']}' → {$iq['question']}..." . PHP_EOL;
        }
        if (count($invalidAnswerQuestions) > 5) {
            $remaining = count($invalidAnswerQuestions) - 5;
            echo "      ... and {$remaining} more" . PHP_EOL;
        }
    }

    echo PHP_EOL;
}

echo str_repeat('=', 70) . PHP_EOL;
echo "📊 SUMMARY" . PHP_EOL;
echo str_repeat('=', 70) . PHP_EOL;
if ($totalIssues === 0) {
    echo "✅ ALL DATA IS VALID!" . PHP_EOL;
} else {
    echo "❌ Total issues found: {$totalIssues}" . PHP_EOL;
    echo "💡 Recommendation: Review and fix the issues above" . PHP_EOL;
}
echo PHP_EOL;
