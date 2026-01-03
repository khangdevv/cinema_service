<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThayKhueWithAnswersSeeder extends Seeder
{
    public function run(): void
    {
        $examId = 3;
        
        // Xóa câu hỏi cũ của exam_id = 3
        DB::connection('tracnghiem')->table('quiz_questions')->where('exam_id', $examId)->delete();
        
        // Đáp án đúng theo thứ tự từng chủ đề
        $answers = [
            // CHỦ ĐỀ 1: 20 câu
            'c','d','b','c','c','b','d','b','b','c','b','c','b','c','c','b','c','c','b','c',
            // CHỦ ĐỀ 2: 20 câu
            'b','c','c','b','c','c','b','c','c','c','b','a','b','c','c','b','b','c','d','b',
            // CHỦ ĐỀ 3: 20 câu
            'c','b','b','b','c','b','b','a','b','c','c','c','b','b','c','d','a','b','b','b',
            // CHỦ ĐỀ 4: 20 câu
            'b','c','d','b','b','a','d','b','b','b','c','b','c','b','b','b','b','c','c','b',
            // CHỦ ĐỀ 5: 20 câu
            'b','c','c','b','b','c','c','c','b','b','c','c','b','b','c','c','b','b','c','c',
            // CHỦ ĐỀ 6: 20 câu
            'b','b','c','c','c','b','b','c','b','b','b','b','c','b','c','b','b','b','c','b',
            // CHỦ ĐỀ 7: 20 câu
            'c','b','c','b','b','c','b','b','b','c','b','b','b','b','a','b','b','b','b','b',
            // CHỦ ĐỀ 8: 20 câu
            'b','c','c','b','b','c','b','b','b','b','b','b','b','b','b','b','a','b','b','c',
            // CHỦ ĐỀ 9: 20 câu
            'b','c','b','c','b','c','b','c','a','b','b','b','b','b','b','b','b','c','b','b',
            // CHỦ ĐỀ 10: 20 câu
            'c','b','b','a','c','b','b','b','b','a','b','b','c','b','b','b','b','b','b','c',
        ];
        
        // Mapping chủ đề
        $topicMap = [
            1 => 1,  // Mã nguồn mở
            2 => 2,  // Linux
            3 => 8,  // VS Code  
            4 => 7,  // LAMP
            5 => 9,  // CMS
            6 => 10, // Python/Django
            7 => 6,  // Bugzilla  
            8 => 4,  // Git
            9 => 5,  // Docker
            10 => 10, // ProjectLibre
        ];
        
        // Đọc file
        $filePath = database_path('on_tap_thay_khue.txt');
        if (!file_exists($filePath)) {
            $filePath = base_path('on_tap_thay_khue.txt');
        }
        
        $content = file_get_contents($filePath);
        $lines = explode("\n", $content);
        
        $questions = [];
        $currentTopic = 1;
        $currentQuestion = null;
        $sortOrder = 1;
        $answerIndex = 0;
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Kiểm tra chủ đề mới
            if (preg_match('/^CHỦ ĐỀ (\d+):/u', $line, $matches)) {
                $currentTopic = (int)$matches[1];
                continue;
            }
            
            // Kiểm tra câu hỏi mới
            if (preg_match('/^Câu \d+:\s*(.+)$/u', $line, $matches)) {
                // Lưu câu hỏi trước đó
                if ($currentQuestion && !empty($currentQuestion['question'])) {
                    // Gán đáp án đúng
                    $currentQuestion['correct_answer'] = $answers[$answerIndex] ?? 'a';
                    $answerIndex++;
                    $questions[] = $currentQuestion;
                }
                
                $currentQuestion = [
                    'exam_id' => $examId,
                    'topic_id' => $topicMap[$currentTopic] ?? 1,
                    'question' => $matches[1],
                    'option_a' => '',
                    'option_b' => '',
                    'option_c' => '',
                    'option_d' => '',
                    'correct_answer' => '',
                    'explanation' => '',
                    'sort_order' => $sortOrder++,
                ];
                continue;
            }
            
            // Kiểm tra đáp án
            if ($currentQuestion) {
                if (preg_match('/^A\.\s*(.+)$/u', $line, $matches)) {
                    $currentQuestion['option_a'] = $matches[1];
                } elseif (preg_match('/^B\.\s*(.+)$/u', $line, $matches)) {
                    $currentQuestion['option_b'] = $matches[1];
                } elseif (preg_match('/^C\.\s*(.+)$/u', $line, $matches)) {
                    $currentQuestion['option_c'] = $matches[1];
                } elseif (preg_match('/^D\.\s*(.+)$/u', $line, $matches)) {
                    $currentQuestion['option_d'] = $matches[1];
                }
            }
        }
        
        // Lưu câu hỏi cuối cùng
        if ($currentQuestion && !empty($currentQuestion['question'])) {
            $currentQuestion['correct_answer'] = $answers[$answerIndex] ?? 'a';
            $questions[] = $currentQuestion;
        }

        // Insert vào database
        foreach ($questions as $q) {
            $q['created_at'] = now();
            $q['updated_at'] = now();
            DB::connection('tracnghiem')->table('quiz_questions')->insert($q);
        }
        
        $this->command->info("✅ Đã thêm " . count($questions) . " câu hỏi với đáp án đúng vào exam_id = {$examId}!");
    }
}
