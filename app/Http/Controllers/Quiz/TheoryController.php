<?php

namespace App\Http\Controllers\Quiz;

use App\Http\Controllers\Controller;
use App\Models\Quiz\QuizTopic;
use App\Models\Quiz\QuizTheory;
use Illuminate\Http\Request;

class TheoryController extends Controller
{
    // Lấy danh sách chủ đề lý thuyết
    public function topics()
    {
        $topics = QuizTopic::orderBy('sort_order')
            ->withCount(['theories', 'questions'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $topics
        ]);
    }

    // Lấy chi tiết chủ đề và các bài lý thuyết
    public function topic($slug)
    {
        $topic = QuizTopic::where('slug', $slug)
            ->with(['theories' => function($q) {
                $q->orderBy('sort_order');
            }])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $topic
        ]);
    }

    // Lấy chi tiết bài lý thuyết
    public function show($slug)
    {
        $theory = QuizTheory::where('slug', $slug)
            ->with('topic')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $theory
        ]);
    }

    // Lấy tất cả lý thuyết (cho trang tổng hợp)
    public function all()
    {
        $topics = QuizTopic::orderBy('sort_order')
            ->with(['theories' => function($q) {
                $q->orderBy('sort_order');
            }])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $topics
        ]);
    }
}
