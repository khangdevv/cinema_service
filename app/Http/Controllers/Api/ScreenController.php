<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Screen;
use Illuminate\Http\Request;

class ScreenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $screen = Screen::query();

        if ($request->has('format')) {
            $screen->where('format', 'LIKE', '%' . $request->format . '%');
        }

        if ($request->has('is_active')) {
            $screen->where('is_active', $request->is_active);
        }

        return $screen->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'format' => 'required|string|max:255',
            'row_count' => 'required|numeric',
            'col_count' => 'required|numeric',
            'is_active' => 'required|boolean',
        ]);

        try {
            $screen = new Screen();
            $screen->code = $request->code;
            $screen->name = $request->name;
            $screen->format = $request->format;
            $screen->row_count = $request->row_count;
            $screen->col_count = $request->col_count;
            $screen->is_active = $request->is_active;
            $screen->save();

            return response()->json([
                'success' => true,
                'message' => 'Screen has been created Successfully',
                'data' => [
                    'screen' => $screen,
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Screen has been created failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Screen $screen)
    {
        return response()->json([
            'success' => true,
            'data' => $screen,
            'message' => 'Screen has been retrieved successfully',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $screen = Screen::find($id);

        if ($screen == null) {
            return response()->json([
                'success' => false,
                'message' => "Screen not exist",
            ], 404);
        }

        $request->validate([
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'format' => 'required|string|max:255',
            'row_count' => 'required|numeric',
            'col_count' => 'required|numeric',
            'is_active' => 'required|boolean',
        ]);

        try {
            $screen->code = $request->code;
            $screen->name = $request->name;
            $screen->format = $request->format;
            $screen->row_count = $request->row_count;
            $screen->col_count = $request->col_count;
            $screen->is_active = $request->is_active;
            $screen->save();

            return response()->json([
                'success' => true,
                'message' => 'Screen has been updated successfully',
                'data' => [
                    'screen' => $screen,
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
        $screen = Screen::find($id);

        if (!$screen) {
            return response()->json([
                'success' => false,
                'message' => "Screen not exist",
            ], 404);
        }

        $screen->delete();
        return response()->json([
            'success' => true,
            'message' => "Screen has been deleted successfully",
            'data' => $screen,
        ], 200);
    }
}
