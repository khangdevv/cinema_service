<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Showtime;
use App\Models\Screen;
use App\Models\SeatLock;
use App\Models\OrderLine;
use App\Models\Seat;
use App\Models\Order;
use App\Models\Movie;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ShowtimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $showtime = Showtime::query()->with(['movie', 'screen']);

        if ($request->has('movie_id')) {
            $showtime->where('movie_id', $request->movie_id);
        }

        if ($request->has('screen_id')) {
            $showtime->where('screen_id', $request->screen_id);
        }

        if ($request->has('status')) {
            $showtime->where('status', $request->status);
        }

        if ($request->has('date')) {
            $date = Carbon::parse($request->date);
            $showtime->whereDate('start_at', $date);
        }

        return response()->json([
            'success' => true,
            'message' => 'Showtime has been show Successfully',
            'data' => $showtime->get(),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'movie_id' => 'required|exists:movie,id',
            'screen_id' => 'required|exists:screen,id',
            'start_at' => 'required|date',
            'base_price' => 'required|numeric|min:0',
            'status' => 'required|in:SCHEDULED,OPEN,CLOSED,CANCELLED',
        ]);

        try {
            // Lấy thông tin movie để tính end_at
            $movie = Movie::find($request->movie_id);
            $startAt = Carbon::parse($request->start_at);
            $endAt = $startAt->copy()->addMinutes($movie->duration_min);

            // Kiểm tra conflict thời gian
            $conflict = Showtime::where('screen_id', $request->screen_id)
                ->where(function ($query) use ($startAt, $endAt) {

                    $query->whereBetween('start_at', [$startAt, $endAt])

                        ->orWhereBetween('end_at', [$startAt, $endAt])

                        ->orWhere(function ($q) use ($startAt, $endAt) {
                            $q->where('start_at', '<=', $startAt)
                              ->where('end_at', '>=', $endAt);
                        });
                })
                ->exists();

            if ($conflict) {
                return response()->json([
                    'success' => false,
                    'message' => 'Screen is already booked for this time slot',
                ], 400);
            }

            $showtime = Showtime::create([
                'movie_id' => $request->movie_id,
                'screen_id' => $request->screen_id,
                'start_at' => $startAt,
                'end_at' => $endAt,
                'base_price' => $request->base_price,
                'status' => $request->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Showtime has been created Successfully',
                'data' => [
                    'showtime' => $showtime->load(['movie', 'screen']),
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Showtime has been created failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Showtime $showtime)
    {
        $showtime->load(['movie', 'screen']);
        
        return response()->json([
            'success' => true,
            'data' => $showtime,
            'message' => 'Showtime has been retrieved successfully',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $showtime = Showtime::find($id);

        if ($showtime == null) {
            return response()->json([
                'success' => false,
                'message' => "Showtime not exist",
            ], 404);
        }

        $request->validate([
            'movie_id' => 'required|exists:movie,id',
            'screen_id' => 'required|exists:screen,id',
            'start_at' => 'required|date',
            'base_price' => 'required|numeric|min:0',
            'status' => 'required|in:SCHEDULED,OPEN,CLOSED,CANCELLED',
        ]);

        try {
            $movie = Movie::find($request->movie_id);
            $startAt = Carbon::parse($request->start_at);
            $endAt = $startAt->copy()->addMinutes($movie->duration_min);

            // Kiểm tra conflict (trừ showtime hiện tại)
            $conflict = Showtime::where('screen_id', $request->screen_id)
                ->where('id', '!=', $id)
                ->where(function ($query) use ($startAt, $endAt) {
                    $query->whereBetween('start_at', [$startAt, $endAt])
                        ->orWhereBetween('end_at', [$startAt, $endAt])
                        ->orWhere(function ($q) use ($startAt, $endAt) {
                            $q->where('start_at', '<=', $startAt)
                              ->where('end_at', '>=', $endAt);
                        });
                })
                ->exists();

            if ($conflict) {
                return response()->json([
                    'success' => false,
                    'message' => 'Screen is already booked for this time slot',
                ], 400);
            }

            $showtime->update([
                'movie_id' => $request->movie_id,
                'screen_id' => $request->screen_id,
                'start_at' => $startAt,
                'end_at' => $endAt,
                'base_price' => $request->base_price,
                'status' => $request->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Showtime has been updated successfully',
                'data' => [
                    'showtime' => $showtime->load(['movie', 'screen']),
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
        $showtime = Showtime::find($id);

        if (!$showtime) {
            return response()->json([
                'success' => false,
                'message' => "Showtime not exist",
            ], 404);
        }

        $hasOrders = Order::where('showtime_id', $id)->exists();

        if ($hasOrders) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete showtime with orders",
            ], 400);
        }

        $showtime->delete();
        return response()->json([
            'success' => true,
            'message' => "Showtime has been deleted successfully",
            'data' => $showtime,
        ], 200);
    }

    public function getSeatMap(string $id)
    {
        $showtime = Showtime::with(['movie', 'screen'])->find($id);

        if (!$showtime) {
            return response()->json([
                'success' => false,
                'message' => 'Showtime not found',
            ], 404);
        }

        // Lấy tất cả ghế của phòng
        $seats = Seat::where('screen_id', $showtime->screen_id)
            ->orderBy('row_label')
            ->orderBy('seat_number')
            ->get();

        // Lấy ghế đã đặt
        $bookedSeats = OrderLine::whereHas('order', function($q) use ($id) {
            $q->where('showtime_id', $id)
              ->where('status', 'PAID');
        })
        ->where('item_type', 'TICKET')
        ->pluck('seat_id')
        ->toArray();

        // Lấy ghế đang lock
        $lockedSeats = SeatLock::where('showtime_id', $id)
            ->where('expires_at', '>', Carbon::now())
            ->pluck('seat_id')
            ->toArray();

        // Tạo danh sách ghế với trạng thái
        $seatList = [];
        $available = 0;
        $booked = 0;
        $locked = 0;

        foreach ($seats as $seat) {
            $status = 'AVAILABLE';
            
            if (in_array($seat->id, $bookedSeats)) {
                $status = 'BOOKED';
                $booked++;
            } elseif (in_array($seat->id, $lockedSeats)) {
                $status = 'LOCKED';
                $locked++;
            } elseif ($seat->is_blocked) {
                $status = 'BLOCKED';
            } else {
                $available++;
            }

            $seatList[] = [
                'id' => $seat->id,
                'row' => $seat->row_label,
                'number' => $seat->seat_number,
                'type' => $seat->seat_type,
                'status' => $status,
                'price' => $showtime->base_price,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'showtime' => [
                    'id' => $showtime->id,
                    'movie' => $showtime->movie->title,
                    'screen' => $showtime->screen->name,
                    'start_at' => $showtime->start_at,
                ],
                'seats' => $seatList,
                'summary' => [
                    'total' => $seats->count(),
                    'available' => $available,
                    'booked' => $booked,
                    'locked' => $locked,
                ],
            ],
        ], 200);
    }
}
