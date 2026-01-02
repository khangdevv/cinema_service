<?php

namespace App\Http\Controllers\Quiz;

use App\Http\Controllers\Controller;
use App\Models\Quiz\QuizExam;
use App\Models\Quiz\QuizQuestion;
use App\Models\Quiz\QuizAttempt;
use App\Models\Quiz\QuizAttemptAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    // Nộp bài và tính điểm
    public function submit(Request $request)
    {
        $request->validate([
            'exam_slug' => 'required|string',
            'answers' => 'required|array',
            'time_spent' => 'required|integer',
            'started_at' => 'required',
        ]);

        $user = $request->user();
        $examSlug = $request->exam_slug;
        $userAnswers = $request->answers; // Format: {question_id: 'a'}
        $timeSpent = $request->time_spent;

        // Lấy đề thi
        $exam = QuizExam::where('slug', $examSlug)->first();
        $examName = $exam ? $exam->name : 'Đề Trộn 50 Câu';
        $examId = $exam ? $exam->id : null;

        // Lấy tất cả câu hỏi được trả lời
        $questionIds = array_keys($userAnswers);
        $questions = QuizQuestion::whereIn('id', $questionIds)->get()->keyBy('id');

        $correctCount = 0;
        $wrongCount = 0;
        $results = [];

        foreach ($userAnswers as $qId => $answer) {
            $question = $questions->get($qId);
            if (!$question) continue;

            $isCorrect = strtolower($answer) === strtolower($question->correct_answer);
            if ($isCorrect) {
                $correctCount++;
            } else {
                $wrongCount++;
            }

            $results[] = [
                'question_id' => $qId,
                'user_answer' => $answer,
                'correct_answer' => $question->correct_answer,
                'is_correct' => $isCorrect,
                'question' => $question->question,
                'options' => $question->options,
                'explanation' => $question->explanation,
            ];
        }

        $totalQuestions = count($userAnswers);
        $score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100, 2) : 0;

        // Lưu kết quả
        DB::beginTransaction();
        try {
            $attempt = QuizAttempt::create([
                'account_id' => $user->id,
                'exam_id' => $examId,
                'exam_name' => $examName,
                'total_questions' => $totalQuestions,
                'correct_answers' => $correctCount,
                'wrong_answers' => $wrongCount,
                'score' => $score,
                'time_spent' => $timeSpent,
                'started_at' => $request->started_at,
                'completed_at' => now(),
            ]);

            foreach ($results as $result) {
                QuizAttemptAnswer::create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $result['question_id'],
                    'user_answer' => $result['user_answer'],
                    'correct_answer' => $result['correct_answer'],
                    'is_correct' => $result['is_correct'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'attempt_id' => $attempt->id,
                    'exam_name' => $examName,
                    'total_questions' => $totalQuestions,
                    'correct_answers' => $correctCount,
                    'wrong_answers' => $wrongCount,
                    'score' => $score,
                    'time_spent' => $timeSpent,
                    'formatted_time' => sprintf('%02d:%02d', floor($timeSpent / 60), $timeSpent % 60),
                    'results' => $results,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lưu kết quả: ' . $e->getMessage()
            ], 500);
        }
    }

    // Lấy lịch sử làm bài của user
    public function history(Request $request)
    {
        $user = $request->user();

        $attempts = QuizAttempt::where('account_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($attempt) {
                return [
                    'id' => $attempt->id,
                    'exam_name' => $attempt->exam_name,
                    'total_questions' => $attempt->total_questions,
                    'correct_answers' => $attempt->correct_answers,
                    'wrong_answers' => $attempt->wrong_answers,
                    'score' => $attempt->score,
                    'time_spent' => $attempt->time_spent,
                    'formatted_time' => $attempt->formatted_time,
                    'completed_at' => $attempt->completed_at->format('d/m/Y H:i'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $attempts
        ]);
    }

    // Xem chi tiết một lần làm bài
    public function attemptDetail($id)
    {
        $attempt = QuizAttempt::with(['answers.question'])
            ->findOrFail($id);

        $results = $attempt->answers->map(function($answer) {
            return [
                'question_id' => $answer->question_id,
                'question' => $answer->question->question,
                'options' => $answer->question->options,
                'user_answer' => $answer->user_answer,
                'correct_answer' => $answer->correct_answer,
                'is_correct' => $answer->is_correct,
                'explanation' => $answer->question->explanation,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $attempt->id,
                'exam_name' => $attempt->exam_name,
                'total_questions' => $attempt->total_questions,
                'correct_answers' => $attempt->correct_answers,
                'wrong_answers' => $attempt->wrong_answers,
                'score' => $attempt->score,
                'time_spent' => $attempt->time_spent,
                'formatted_time' => $attempt->formatted_time,
                'completed_at' => $attempt->completed_at->format('d/m/Y H:i'),
                'results' => $results,
            ]
        ]);
    }
}
