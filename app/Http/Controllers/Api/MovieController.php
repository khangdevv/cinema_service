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

        $movie->where('is_active', true);

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
            'message' => 'Movie has been show Successfully',
            'data' => $movie->get(),
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

    public function generateSchedule(Request $request)
    {
        // validate
        $request->validate([
            'title' => 'required|string|max:255',
            'duration_min' => 'required|numeric',
            'genre' => 'required|string',
            'poster' => 'required|url',
            'rating_code' => 'required|string',
            'days' => 'required|numberic|min:0|max:2',
            'screens' => 'required|numberic|min:0|max:8',
            'base_price' => 'required|numberic|min:0',
        ]);

        // time
        $timeSlots = [
            ['hour' => 9, 'minute' => 0],   // 9:00
            ['hour' => 13, 'minute' => 30], // 13:30
            ['hour' => 17, 'minute' => 0],  // 17:00
            ['hour' => 20, 'minute' => 30], // 20:30
        ];

        try {
            // create movie
            $movie = Movie::create([
                'title' => $request->title,
                'duration_min' => $request->duration_min,
                'genre' => $request->genre,
                'poster' => $request->poster,
                'rating_code' => $request->rating_code,
                'is_active' => true,
            ]);

            $movie = Movie::find($movie->id);

            // get screen 
            $screen = Screen::query(select('id'))->where('is_active', true);
            $screenId = array();

            foreach ($screen as $key => $value) {
                $screenId[] = $value->id;
            }

            // check screen active
            if (isEmpty($screen)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No screens available',
                    'data' => [
                        'movie' => $movie,
                    ],
                ], 400);
            }

            // get tommorrow
            $startDate = Carbon::now()->addDays();

            for ($i = 0; $i < $request->days; $i++) {

            }

            $showtimes = Showtime::create([
                'movie_id' => $request->id,
                'screen_id' => random($screenId, ),
                'start_at' => '',
                'end_at' => '',
                'base_price' => $request->base_price,
                'status' => 'OPEN',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Movie created with showtimes successfully',
                'data' => [
                    'movie' => $movie,
                    'showtimes_created' => count($createdShowtimes),
                    'screens_used' => $screens->pluck('name'),
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
}
