<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Screen;
use App\Models\Seat;
use App\Models\Showtime;
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

        if ($request->has('name')) {
            $screen->where('name', 'LIKE', '%' . $request->name . '%');
        }

        if ($request->has('is_active')) {
            $screen->where('is_active', $request->is_active);
        }

        return response()->json([
            'success' => true,
            'message' => 'Screen has been show Successfully',
            'data' => $screen->get(),
        ], 200);
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
            $screen = Screen::create([
                'code' => $request->code,
                'name' => $request->name,
                'format' => $request->format,
                'row_count' => $request->row_count,
                'col_count' => $request->col_count,
                'is_active' => $request->is_active,
            ]);

            $screen = Screen::where('code', $request->code)->first();

            $seats = [];
            $rowLabels = range('A', 'Z');

            for ($row = 0; $row < $screen->row_count; $row++) {
                for ($col = 1; $col <= $screen->col_count; $col++) {
                    $seats[] = [
                        'screen_id' => $screen->id,
                        'row_label' => $rowLabels[$row],
                        'seat_number' => $col,
                        'seat_type' => 'STANDARD',
                        'is_aisle' => false,
                        'is_blocked' => false,
                    ];
                }
            }

            Seat::insert($seats);

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
            $sizeChanged = ($screen->row_count != $request->row_count)
                || ($screen->col_count != $request->col_count);

            if ($sizeChanged) {

                $hasShowtimes = Showtime::where("screen_id", $id)->exists();

                if ($hasShowtimes) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot change screen size because there are showtimes',
                    ], 400);
                }

                // Xóa tất cả ghế cũ
                Seat::where('screen_id', $id)->delete();
                // Tạo ghế mới
                $seats = [];
                $rowLabels = range('A', 'Z');
                for ($row = 0; $row < $request->row_count; $row++) {
                    for ($col = 1; $col <= $request->col_count; $col++) {
                        $seats[] = [
                            'screen_id' => $screen->id,
                            'row_label' => $rowLabels[$row],
                            'seat_number' => $col,
                            'seat_type' => 'STANDARD',
                            'is_aisle' => false,
                            'is_blocked' => false,
                        ];
                    }
                }
                Seat::insert($seats);
            }

            $screen->update([
                'code' => $request->code,
                'name' => $request->name,
                'format' => $request->format,
                'row_count' => $request->row_count,
                'col_count' => $request->col_count,
                'is_active' => $request->is_active,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Screen has been updated successfully',
                'data' => [
                    'screen' => $screen,
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
        $screen = Screen::find($id);

        if (!$screen) {
            return response()->json([
                'success' => false,
                'message' => "Screen not exist",
            ], 404);
        }

        $hasShowtimes = Showtime::where("screen_id", $id)->exists();

        if ($hasShowtimes) {

            $screen->is_active = false;
            $screen->save();

            return response()->json([
                'success' => true,
                'message' => "Screen has been deactive successfully",
                'data' => $screen,
            ], 200);
        } else {
            Seat::where('screen_id', $id)->delete();

            $screen->delete();

            return response()->json([
                'success' => true,
                'message' => "Screen has been deleted successfully",
                'data' => $screen,
            ], 200);
        }
    }
}
