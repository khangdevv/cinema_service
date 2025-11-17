<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Movie::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'duration_min' => 'required|numeric',
            'rating_code' => 'nullable|string',
        ]);

        try {
            $movie = new Movie();
            $movie->title = $request->title;
            $movie->duration_min = $request->duration_min;
            $movie->rating_code = $request->rating_code;
            $movie->save();


            return response()->json([
            'success' => true,
            'message' => 'Movie has been created Successfully',
            'data' => [
                'movie' => $movie,
                ],
            ], 201);

        }
        catch (\Exception $e)
        {
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
            'rating_code' => 'nullable|string',
        ]);

        try {

            $movie->title = $request->title;
            $movie->duration_min = $request->duration_min;
            $movie->rating_code = $request->rating_code;
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
            ],404);
        }

        $movie->delete();
        return response()->json([
            'success' => true,
            'message' => "Movie has been deleted successfully",
            'data' => $movie,
        ],200);
    }
}
