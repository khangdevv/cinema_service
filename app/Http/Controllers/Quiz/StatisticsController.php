<?php

namespace App\Http\Controllers\Quiz;

use App\Http\Controllers\Controller;
use App\Models\Quiz\QuizAttempt;
use App\Models\Quiz\QuizExam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    // Thống kê tổng quan của user
    public function overview(Request $request)
    {
        $user = $request->user();

        $stats = QuizAttempt::where('account_id', $user->id)
            ->whereNotNull('completed_at')
            ->selectRaw('
                COUNT(*) as total_attempts,
                AVG(score) as avg_score,
                MAX(score) as best_score,
                SUM(correct_answers) as total_correct,
                SUM(wrong_answers) as total_wrong,
                SUM(time_spent) as total_time
            ')
            ->first();

        $examStats = QuizAttempt::where('account_id', $user->id)
            ->whereNotNull('completed_at')
            ->selectRaw('exam_name, COUNT(*) as attempts, AVG(score) as avg_score, MAX(score) as best_score')
            ->groupBy('exam_name')
            ->get();

        $totalQuestions = ($stats->total_correct ?? 0) + ($stats->total_wrong ?? 0);
        $accuracy = $totalQuestions > 0
            ? round(($stats->total_correct / $totalQuestions) * 100, 2)
            : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'overview' => [
                    'total_attempts' => $stats->total_attempts ?? 0,
                    'avg_score' => round($stats->avg_score ?? 0, 2),
                    'best_score' => round($stats->best_score ?? 0, 2),
                    'total_correct' => $stats->total_correct ?? 0,
                    'total_wrong' => $stats->total_wrong ?? 0,
                    'total_time_minutes' => round(($stats->total_time ?? 0) / 60, 1),
                    'accuracy' => $accuracy,
                ],
                'exam_stats' => $examStats
            ]
        ]);
    }

    // Dữ liệu cho biểu đồ tiến độ
    public function progress(Request $request)
    {
        $user = $request->user();

        // Lấy 20 lần làm bài gần nhất
        $attempts = QuizAttempt::where('account_id', $user->id)
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'asc')
            ->limit(20)
            ->get()
            ->map(function($attempt, $index) {
                return [
                    'index' => $index + 1,
                    'id' => $attempt->id,
                    'exam_name' => $attempt->exam_name,
                    'score' => $attempt->score,
                    'correct' => $attempt->correct_answers,
                    'wrong' => $attempt->wrong_answers,
                    'time_spent' => $attempt->time_spent,
                    'date' => $attempt->completed_at->format('d/m'),
                    'datetime' => $attempt->completed_at->format('d/m/Y H:i'),
                ];
            });

        // Thống kê theo ngày (30 ngày gần nhất)
        $dailyStats = QuizAttempt::where('account_id', $user->id)
            ->whereNotNull('completed_at')
            ->where('completed_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(completed_at) as date, COUNT(*) as attempts, AVG(score) as avg_score')
            ->groupBy(DB::raw('DATE(completed_at)'))
            ->orderBy('date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'attempts' => $attempts,
                'daily_stats' => $dailyStats
            ]
        ]);
    }

    // Bảng xếp hạng
    public function leaderboard()
    {
        $leaderboard = QuizAttempt::whereNotNull('completed_at')
            ->selectRaw('
                account_id,
                COUNT(*) as total_attempts,
                ROUND(AVG(score), 2) as avg_score,
                MAX(score) as best_score
            ')
            ->with('account:id,full_name,avatar')
            ->groupBy('account_id')
            ->orderByDesc('avg_score')
            ->limit(20)
            ->get()
            ->map(function($item, $index) {
                return [
                    'rank' => $index + 1,
                    'user' => [
                        'id' => $item->account->id ?? 0,
                        'name' => $item->account->full_name ?? 'Anonymous',
                        'avatar' => $item->account->avatar,
                    ],
                    'avg_score' => $item->avg_score,
                    'best_score' => $item->best_score,
                    'total_attempts' => $item->total_attempts,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $leaderboard
        ]);
    }
}
