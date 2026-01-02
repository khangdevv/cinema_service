<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MixedExamSeeder extends Seeder
{
    public function run()
    {
        // Delete existing questions for exam 4
        DB::table('quiz_questions')->where('exam_id', 4)->delete();

        // Get 50 random questions from exams 1, 2, 3
        $allQuestions = DB::table('quiz_questions')
            ->whereIn('exam_id', [1, 2, 3])
            ->inRandomOrder()
            ->limit(50)
            ->get();

        // Insert as exam 4 questions
        foreach ($allQuestions as $q) {
            DB::table('quiz_questions')->insert([
                'exam_id' => 4,
                'question' => $q->question,
                'option_a' => $q->option_a,
                'option_b' => $q->option_b,
                'option_c' => $q->option_c,
                'option_d' => $q->option_d,
                'correct_answer' => $q->correct_answer,
                'explanation' => $q->explanation,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Created 50 random mixed questions for Exam 4!');
    }
}
