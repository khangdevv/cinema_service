<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TracNghiemNguonMoSeeder extends Seeder
{
    public function run(): void
    {
        $examId = 2;
        
        DB::connection('tracnghiem')->table('quiz_questions')->where('exam_id', $examId)->delete();
        
        // Đáp án đúng theo thứ tự câu hỏi trong file (đã được user cung cấp)
        $answers = [
            // CHƯƠNG 1 (7 câu): 3,5,6,14,17,19,20
            'b','d','b','b','a','d','d',
            // CHƯƠNG 2 phần lặp: OSI(d), GIMP(a)
            'd','a',
            // CHƯƠNG 2 Linux: 5,6,7,10,11,12,13,14,15,16
            'b','a','a','d','a','a','a','a','a','a',
            // 17,18,19,23,25,26,27,28,29,30
            'b','b','a','c','a','a','a','b','a','a',
            // 31,32,33,34,35,36,37,38,39,40
            'a','b','a','a','a','a','a','a','a','d',
            // 41,42,43,44,45,46,47,51,52,53
            'a','a','a','d','a','a','a','a','a','a',
            // 54,55,56,57,58,59,60,61,62,63
            'c','a','a','a','a','a','a','a','a','a',
            // 65,66,67,68,69,70,71,72,73,74
            'c','a','b','a','a','a','b','a','a','a',
            // 75,76,77,78,79,80,81,82,83,84
            'a','a','d','a','a','a','a','a','d','c',
            // 85,86,87,88,89,90,91,92,93,94,95
            'a','a','a','a','a','a','a','a','a','a','a',
            // CHƯƠNG 3&4: 1-10
            'a','a','a','a','a','a','d','c','d','a',
            // 11-20
            'a','a','a','a','a','a','a','a','a','a',
            // 21-30
            'a','b','a','a','a','a','a','a','a','a',
            // 31-34
            'a','a','a','a',
        ];
        
        $topicMap = [1 => 1, 2 => 2, 3 => 3, 4 => 3];
        
        $filePath = base_path('trac_nghiem_nguon_mo.txt');
        $content = file_get_contents($filePath);
        $lines = explode("\n", $content);
        
        $questions = [];
        $currentTopic = 1;
        $currentQuestion = null;
        $sortOrder = 1;
        $answerIndex = 0;
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            if (preg_match('/^(Chương|CHƯƠNG|Chuong)\s*(\d+)/ui', $line, $matches)) {
                $currentTopic = (int)$matches[2];
                continue;
            }
            
            if (preg_match('/^Câu\s*(\d+)[\.:]\s*(.+)$/u', $line, $matches)) {
                if ($currentQuestion && !empty($currentQuestion['question'])) {
                    $currentQuestion['correct_answer'] = $answers[$answerIndex] ?? 'a';
                    $answerIndex++;
                    $questions[] = $currentQuestion;
                }
                
                $currentQuestion = [
                    'exam_id' => $examId,
                    'topic_id' => $topicMap[$currentTopic] ?? 1,
                    'question' => $matches[2],
                    'option_a' => '',
                    'option_b' => '',
                    'option_c' => '',
                    'option_d' => '',
                    'correct_answer' => 'a',
                    'explanation' => '',
                    'sort_order' => $sortOrder++,
                ];
                continue;
            }
            
            if ($currentQuestion) {
                if (preg_match('/^a[\.\t]\s*(.+)$/ui', $line, $matches)) {
                    $currentQuestion['option_a'] = trim($matches[1]);
                } elseif (preg_match('/^b[\.\t]\s*(.+)$/ui', $line, $matches)) {
                    $currentQuestion['option_b'] = trim($matches[1]);
                } elseif (preg_match('/^c[\.\t]\s*(.+)$/ui', $line, $matches)) {
                    $currentQuestion['option_c'] = trim($matches[1]);
                } elseif (preg_match('/^d[\.\t]\s*(.+)$/ui', $line, $matches)) {
                    $currentQuestion['option_d'] = trim($matches[1]);
                }
            }
        }
        
        if ($currentQuestion && !empty($currentQuestion['question'])) {
            $currentQuestion['correct_answer'] = $answers[$answerIndex] ?? 'a';
            $questions[] = $currentQuestion;
        }

        foreach ($questions as $q) {
            $q['created_at'] = now();
            $q['updated_at'] = now();
            DB::connection('tracnghiem')->table('quiz_questions')->insert($q);
        }
        
        $this->command->info("✅ Đã thêm " . count($questions) . " câu hỏi với đáp án đúng vào exam_id = {$examId}!");
    }
}
