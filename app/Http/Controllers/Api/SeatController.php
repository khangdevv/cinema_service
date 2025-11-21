<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Seat;
use App\Models\Screen;
use Illuminate\Http\Request;

class SeatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $seat = Seat::query()->with('screen');

        if ($request->has('screen_id')) {
            $seat->where('screen_id', $request->screen_id);
        }

        if ($request->has('seat_type')) {
            $seat->where('seat_type', $request->seat_type);
        }

        if ($request->has('is_blocked')) {
            $seat->where('is_blocked', $request->is_blocked);
        }

        return $seat->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'screen_id' => 'required|exists:screen,id',
            'row_label' => 'required|string|max:5',
            'seat_number' => 'required|numeric',
            'seat_type' => 'required|in:STANDARD,VIP,COUPLE,ACCESSIBLE',
            'is_aisle' => 'required|boolean',
            'is_blocked' => 'required|boolean',
        ]);

        try {
            $seat = new Seat();
            $seat->screen_id = $request->screen_id;
            $seat->row_label = $request->row_label;
            $seat->seat_number = $request->seat_number;
            $seat->seat_type = $request->seat_type;
            $seat->is_aisle = $request->is_aisle;
            $seat->is_blocked = $request->is_blocked;
            $seat->save();

            return response()->json([
                'success' => true,
                'message' => 'Seat has been created Successfully',
                'data' => [
                    'seat' => $seat,
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Seat has been created failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Seat $seat)
    {
        $seat->load('screen');
        
        return response()->json([
            'success' => true,
            'data' => $seat,
            'message' => 'Seat has been retrieved successfully',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $seat = Seat::find($id);

        if ($seat == null) {
            return response()->json([
                'success' => false,
                'message' => "Seat not exist",
            ], 404);
        }

        $request->validate([
            'screen_id' => 'required|exists:screen,id',
            'row_label' => 'required|string|max:5',
            'seat_number' => 'required|numeric',
            'seat_type' => 'required|in:STANDARD,VIP,COUPLE,ACCESSIBLE',
            'is_aisle' => 'required|boolean',
            'is_blocked' => 'required|boolean',
        ]);

        try {
            $seat->screen_id = $request->screen_id;
            $seat->row_label = $request->row_label;
            $seat->seat_number = $request->seat_number;
            $seat->seat_type = $request->seat_type;
            $seat->is_aisle = $request->is_aisle;
            $seat->is_blocked = $request->is_blocked;
            $seat->save();

            return response()->json([
                'success' => true,
                'message' => 'Seat has been updated successfully',
                'data' => [
                    'seat' => $seat,
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
        $seat = Seat::find($id);

        if (!$seat) {
            return response()->json([
                'success' => false,
                'message' => "Seat not exist",
            ], 404);
        }

        $seat->delete();
        return response()->json([
            'success' => true,
            'message' => "Seat has been deleted successfully",
            'data' => $seat,
        ], 200);
    }

    /**
     * Generate seats for a screen automatically
     */
    public function generateSeats(Request $request, string $screenId)
    {
        $screen = Screen::find($screenId);

        if (!$screen) {
            return response()->json([
                'success' => false,
                'message' => "Screen not exist",
            ], 404);
        }

        try {

            Seat::where('screen_id', $screenId)->delete();

            $seats = [];
            $rowLabels = range('A', 'Z');

            for ($row = 0; $row < $screen->row_count; $row++) {
                for ($col = 1; $col <= $screen->col_count; $col++) {
                    $seats[] = [
                        'screen_id' => $screenId,
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
                'message' => 'Seats generated successfully',
                'data' => [
                    'total_seats' => count($seats),
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Generate seats failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
