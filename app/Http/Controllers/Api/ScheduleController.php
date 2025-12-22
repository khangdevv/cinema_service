<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Screen;
use App\Models\Showtime;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function createMovieWithSchedule(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'duration_min' => 'required|numeric',
            'genre' => 'nullable|string',
            'poster' => 'nullable|url',
            'rating_code' => 'nullable|string',
            'days' => 'required|numeric|min:1|max:3',
            'screens_count' => 'required|numeric|min:1|max:8',
            'base_price' => 'required|numeric|min:0',
        ]);

        try {
            $movie = Movie::create([
                'title' => $request->title,
                'duration_min' => $request->duration_min,
                'genre' => $request->genre,
                'poster' => $request->poster,
                'rating_code' => $request->rating_code,
                'is_active' => true,
            ]);

            // Gọi hàm helper để tạo schedule
            $result = $this->generateScheduleForMovie(
                $movie,
                $request->days,
                $request->screens_count,
                $request->base_price
            );

            if (!$result['success']) {
                // Nếu không tạo được schedule, xóa phim vừa tạo
                $movie->delete();
                return response()->json($result, 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Movie created with showtimes successfully',
                'data' => [
                    'movie' => $movie,
                    'showtimes_created' => $result['count'],
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create movie with schedule',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function generateForExisting(Request $request, Movie $movie)
    {
        $request->validate([
            'days' => 'required|numeric|min:1|max:3',
            'screens_count' => 'required|numeric|min:1|max:8',
            'base_price' => 'required|numeric|min:0',
        ]);

        try {
            $result = $this->generateScheduleForMovie(
                $movie,
                $request->days,
                $request->screens_count,
                $request->base_price
            );

            if (!$result['success']) {
                return response()->json($result, 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Showtimes generated successfully for existing movie',
                'data' => [
                    'movie' => $movie,
                    'showtimes_created' => $result['count'],
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate schedule',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    private function generateScheduleForMovie(Movie $movie, int $days, int $screensCount, float $basePrice): array
    {
        // Lấy danh sách phòng active
        $allScreens = Screen::where('is_active', true)->get();

        if ($allScreens->count() == 0) {
            return [
                'success' => false,
                'message' => 'No screens available',
                'count' => 0,
            ];
        }

        // Lấy ID các phòng
        $screenIds = $allScreens->pluck('id')->toArray();

        // Random số lượng phòng cần lấy
        $selectedCount = min($screensCount, count($screenIds));
        $randomKeys = array_rand($screenIds, $selectedCount);

        // Nếu chỉ random 1 phần tử, array_rand trả về số, không phải mảng
        if (!is_array($randomKeys)) {
            $randomKeys = [$randomKeys];
        }

        $selectedScreenIds = [];
        foreach ($randomKeys as $key) {
            $selectedScreenIds[] = $screenIds[$key];
        }

        // Lấy phòng đã chọn
        $screens = Screen::whereIn('id', $selectedScreenIds)->get();

        // Tạo suất chiếu từ ngày hôm sau
        $startDate = Carbon::now()->addDay();
        $createdCount = 0;

        for ($day = 0; $day < $days; $day++) {
            foreach ($screens as $screen) {
                $startAt = Carbon::parse($startDate->copy()
                    ->addDays($day)
                    ->setHour(rand(8, 20))
                    ->setMinute(fake()->randomElement([0, 15, 30, 45]))
                    ->setSecond(0));

                // Tính giờ kết thúc
                $endAt = $startAt->copy()->addMinutes($movie->duration_min);

                // Tạo showtime
                Showtime::create([
                    'movie_id' => $movie->id,
                    'screen_id' => $screen->id,
                    'start_at' => $startAt,
                    'end_at' => $endAt,
                    'base_price' => $basePrice,
                    'status' => 'OPEN',
                ]);

                $createdCount++;
            }
        }

        return [
            'success' => true,
            'message' => 'Schedule created successfully',
            'count' => $createdCount,
        ];
    }
}
