<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Showtime;
use Illuminate\Http\Request;

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

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $movie->where(function ($query) use ($keyword) {
                $query->where('title', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('genre', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('rating_code', 'LIKE', '%' . $keyword . '%');
            });
        }

        return response()->json([
            'success' => true,
            'message' => 'Movies retrieved successfully',
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
                    'movie' => $movie
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
            'title' => 'string|max:255',
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
                    'movie' => $movie
                ],
            ], 200);

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

        $hasShowtimes = Showtime::where('movie_id', $id)->exists();

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
}
