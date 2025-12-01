<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Screen;
use App\Models\Showtime;
use Illuminate\Http\Request;

use function Laravel\Prompts\select;
use function PHPUnit\Framework\isEmpty;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $movie = Movie::query();

        // Chỉ filter is_active nếu không truyền tham số show_all
        if (!$request->has('show_all')) {
            $movie->where('is_active', true);
        }

        if ($request->has('title')) {
            $movie->where('title', 'LIKE', '%' . $request->title . '%');
        }

        if ($request->has('genre')) {
            $movie->where('genre', 'LIKE', '%' . $request->genre . '%');
        }

        if ($request->has('rating_code')) {
            $movie->where('rating_code', 'LIKE', '%' . $request->rating_code . '%');
        }

        return response()->json([
            'success' => true,
            'message' => 'Movies retrieved successfully',
            'data' => $movie->get()
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'duration_min' => 'required|numeric',
            'genre' => 'nullable|string',
            'poster' => 'nullable|url',
            'rating_code' => 'nullable|string',
        ]);

        try {
            $movie = new Movie();
            $movie->title = $request->title;
            $movie->duration_min = $request->duration_min;
            $movie->genre = $request->genre;
            $movie->poster = $request->poster;
            $movie->rating_code = $request->rating_code;
            $movie->is_active = true;
            $movie->save();

            return response()->json([
                'success' => true,
                'message' => 'Movie has been created Successfully',
                'data' => [
                    'movie' => $movie,
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Movie has been created failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Movie $movie)
    {
        return response()->json([
            'success' => true,
            'data' => $movie,
            'message' => 'Movie has been retrieved successfully',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $movie = Movie::find($id);

        if ($movie == null) {
            return response()->json([
                'success' => false,
                'message' => "Movie not exist",
            ], 404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'duration_min' => 'required|numeric',
            'genre' => 'nullable|string',
            'poster' => 'nullable|url',
            'rating_code' => 'nullable|string',
        ]);

        try {
            $movie->title = $request->title;
            $movie->duration_min = $request->duration_min;
            $movie->genre = $request->genre;
            $movie->poster = $request->poster;
            $movie->rating_code = $request->rating_code;
            $movie->is_active = true;
            $movie->save();

            return response()->json([
                'success' => true,
                'message' => 'Movie has been updated successfully',
                'data' => [
                    'movie' => $movie,
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Update failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $movie = Movie::find($id);

        if (!$movie) {
            return response()->json([
                'success' => false,
                'message' => "Movie not exist",
            ], 404);
        }

        $hasShowtimes = \App\Models\Showtime::where('movie_id', $id)->exists();

        if ($hasShowtimes) {
            $movie->is_active = false;
            $movie->save();

            return response()->json([
                'success' => true,
                'message' => "Movie has been deactivated",
                'data' => $movie,
            ], 200);
        } else {
            $movie->delete();

            return response()->json([
                'success' => true,
                'message' => "Movie has been deleted sucessfully",
                'data' => $movie,
            ], 200);
        }
    }

    /**
     * Create movie with auto showtimes - ĐƠN GIẢN NHẤT
     */
    public function generateSchedule(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'duration_min' => 'required|numeric',
            'genre' => 'nullable|string',
            'poster' => 'nullable|url',
            'rating_code' => 'nullable|string',
            'days' => 'required|numeric|min:1|max:30',
            'screens_count' => 'required|numeric|min:1|max:8',
            'base_price' => 'required|numeric|min:0',
        ]);

        try {
            // tạo phim
            $movie = Movie::create([
                'title' => $request->title,
                'duration_min' => $request->duration_min,
                'genre' => $request->genre,
                'poster' => $request->poster,
                'rating_code' => $request->rating_code,
                'is_active' => true,
            ]);

            // danh sách phòng active
            $allScreens = \App\Models\Screen::where('is_active', true)->get();

            if ($allScreens->count() == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No screens available',
                ], 400);
            }

            // lấy id
            $screenIds = [];
            foreach ($allScreens as $screen) {
                $screenIds[] = $screen->id;
            }

            // random số lượng cần lấy 
            // nếu giả định có ít rạp thì lấy ít rạp để đỡ lỗi
            $selectedCount = min($request->screens_count, count($screenIds));
            $randomKeys = array_rand($screenIds, $selectedCount);

            // Nếu chỉ random 1 phần tử, array_rand trả về số, không phải mảng
            if (!is_array($randomKeys)) {
                $randomKeys = [$randomKeys];
            }

            $selectedScreenIds = [];
            foreach ($randomKeys as $key) {
                $selectedScreenIds[] = $screenIds[$key];
            }

            // lấy phòng mình đã chọn 
            $screens = Screen::whereIn('id', $selectedScreenIds)->get();

            // tạo suất chiếu
            // bắt đầu từ ngày hôm sau so với hiện tại 
            $startDate = \Carbon\Carbon::now()->addDay();
            $createdCount = 0;

            // tạo
            for ($day = 0; $day < $request->days; $day++) {
                foreach ($screens as $screen) {
                    $startAt = $startDate->copy()
                        ->addDays($day)
                        ->setHour(fake()->randomNumber(8, 20))
                        ->setMinute(fake()->randomElement([0, 15, 30, 45]))
                        ->setSecond(0);

                    // tính giờ kết thúc bằng cách cộng thời lượng phim với giờ bắt đầu vô nhau 
                    $endAt = $startAt->copy()->addMinutes($movie->duration_min);

                    // Tạo showtime
                    Showtime::create([
                        'movie_id' => $movie->id,
                        'screen_id' => $screen->id,
                        'start_at' => $startAt,
                        'end_at' => $endAt,
                        'base_price' => $request->base_price,
                        'status' => 'OPEN',
                    ]);

                    $createdCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Movie created with showtimes successfully',
                'data' => [
                    'movie' => $movie,
                    'showtimes_created' => $createdCount,
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create movie',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
