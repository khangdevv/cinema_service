<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThayKhueSeeder extends Seeder
{
    public function run(): void
    {
        $examId = 3; // Thêm vào exam_id = 2
        
        // Xóa câu hỏi cũ của exam_id = 2
        DB::connection('tracnghiem')->table('quiz_questions')->where('exam_id', $examId)->delete();
        
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
            $questions[] = $currentQuestion;
        }

        // Xác định correct_answer dựa vào nội dung
        // Vì file không có đáp án đúng rõ ràng, ta để mặc định là 'a'
        // Bạn có thể cập nhật sau
        foreach ($questions as &$q) {
            if (empty($q['correct_answer'])) {
                $q['correct_answer'] = 'a'; // Mặc định
            }
            $q['created_at'] = now();
            $q['updated_at'] = now();
        }
        
        // Insert vào database
        foreach ($questions as $q) {
            DB::connection('tracnghiem')->table('quiz_questions')->insert($q);
        }
        
        $this->command->info("✅ Đã thêm " . count($questions) . " câu hỏi vào exam_id = {$examId}!");
    }
}
