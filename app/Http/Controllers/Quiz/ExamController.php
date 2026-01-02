<?php

namespace App\Http\Controllers\Quiz;

use App\Http\Controllers\Controller;
use App\Models\Quiz\QuizExam;
use App\Models\Quiz\QuizQuestion;
use App\Models\Quiz\QuizAttempt;
use App\Models\Quiz\QuizAttemptAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    // Danh sách đề thi
    public function index()
    {
        $exams = QuizExam::select('id', 'name', 'slug', 'description', 'type', 'time_limit')
            ->withCount('questions')
            ->get()
            ->map(function ($exam) {
                return [
                    'id' => $exam->id,
                    'name' => $exam->name,
                    'slug' => $exam->slug,
                    'description' => $exam->description,
                    'type' => $exam->type,
                    'time_limit' => $exam->time_limit,
                    'total_questions' => $exam->questions_count,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $exams
        ]);
    }

    // Chi tiết đề thi + câu hỏi
    public function show($slug)
    {
        $exam = QuizExam::where('slug', $slug)->first();

        if (!$exam) {
            return response()->json([
                'success' => false,
                'message' => 'Đề thi không tồn tại'
            ], 404);
        }

        $questions = QuizQuestion::where('exam_id', $exam->id)
            ->select('id', 'question', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer')
            ->get();

        $formattedQuestions = $questions->map(function ($q) {
            return [
                'id' => $q->id,
                'question' => $q->question,
                'options' => [
                    'a' => $q->option_a,
                    'b' => $q->option_b,
                    'c' => $q->option_c,
                    'd' => $q->option_d,
                ],
                'correct_answer' => $q->correct_answer,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $exam->id,
                'name' => $exam->name,
                'slug' => $exam->slug,
                'description' => $exam->description,
                'type' => $exam->type,
                'time_limit' => $exam->time_limit,
                'total_questions' => count($formattedQuestions),
            ],
            'questions' => $formattedQuestions
        ]);
    }

    // Nộp bài
    public function submit(Request $request)
    {
        $request->validate([
            'exam_slug' => 'required|string',
            'answers' => 'required|array',
            'time_spent' => 'required|integer',
        ]);

        $user = $request->user();
        $examSlug = $request->exam_slug;
        $userAnswers = $request->answers;
        $timeSpent = $request->time_spent;

        $exam = QuizExam::where('slug', $examSlug)->first();
        $examName = $exam ? $exam->name : 'Đề Trộn 50 Câu';
        $examId = $exam ? $exam->id : null;

        $questionIds = array_keys($userAnswers);
        $questions = QuizQuestion::whereIn('id', $questionIds)->get()->keyBy('id');

        $correctCount = 0;
        $wrongCount = 0;
        $results = [];

        foreach ($userAnswers as $qId => $answer) {
            $question = $questions->get($qId);
            if (!$question)
                continue;

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
                'options' => [
                    'a' => $question->option_a,
                    'b' => $question->option_b,
                    'c' => $question->option_c,
                    'd' => $question->option_d,
                ],
                'explanation' => $question->explanation,
            ];
        }

        $totalQuestions = count($userAnswers);
        $score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100, 2) : 0;

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
                'started_at' => now()->subSeconds($timeSpent),
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
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    // Lịch sử làm bài
    public function history(Request $request)
    {
        $user = $request->user();

        $attempts = QuizAttempt::where('account_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($attempt) {
                return [
                    'id' => $attempt->id,
                    'exam_name' => $attempt->exam_name,
                    'total_questions' => $attempt->total_questions,
                    'correct_answers' => $attempt->correct_answers,
                    'wrong_answers' => $attempt->wrong_answers,
                    'score' => $attempt->score,
                    'time_spent' => $attempt->time_spent,
                    'formatted_time' => sprintf('%02d:%02d', floor($attempt->time_spent / 60), $attempt->time_spent % 60),
                    'completed_at' => $attempt->completed_at ? $attempt->completed_at->format('d/m/Y H:i') : null,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $attempts
        ]);
    }

    // Xem chi tiết kết quả - RETURN ALL QUESTIONS
    public function attemptDetail($id, Request $request)
    {
        $user = $request->user();

        $attempt = QuizAttempt::with(['answers.question'])
            ->where('account_id', $user->id)
            ->findOrFail($id);

        // Get ALL questions from the exam
        $exam = QuizExam::where('id', $attempt->exam_id)->first();
        $allQuestions = $exam
            ? QuizQuestion::where('exam_id', $exam->id)->get()
            : QuizQuestion::whereIn('id', $attempt->answers->pluck('question_id'))->get();

        // Create a map of user answers by question_id
        $userAnswersMap = $attempt->answers->keyBy('question_id');

        // Build results for ALL questions
        $results = $allQuestions->map(function ($question) use ($userAnswersMap) {
            $userAnswer = $userAnswersMap->get($question->id);

            // If user didn't answer, mark as incorrect with null answer
            $answeredOption = $userAnswer ? $userAnswer->user_answer : null;
            $isCorrect = $userAnswer ? $userAnswer->is_correct : false;

            return [
                'question_id' => $question->id,
                'question' => $question->question,
                'options' => [
                    'a' => $question->option_a,
                    'b' => $question->option_b,
                    'c' => $question->option_c,
                    'd' => $question->option_d,
                ],
                'user_answer' => $answeredOption, // null if not answered
                'correct_answer' => $question->correct_answer,
                'is_correct' => $isCorrect, // false if not answered
                'explanation' => $question->explanation,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $attempt->id,
                'exam_name' => $attempt->exam_name,
                'total_questions' => $allQuestions->count(), // Total questions in exam
                'correct_answers' => $attempt->correct_answers,
                'wrong_answers' => $attempt->wrong_answers,
                'score' => $attempt->score,
                'time_spent' => $attempt->time_spent,
                'formatted_time' => sprintf('%02d:%02d', floor($attempt->time_spent / 60), $attempt->time_spent % 60),
                'completed_at' => $attempt->completed_at ? $attempt->completed_at->format('d/m/Y H:i') : null,
                'results' => $results, // ALL questions, not just answered ones
            ]
        ]);
    }
}
