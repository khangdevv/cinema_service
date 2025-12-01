<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Showtime;
use App\Models\Screen;
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

    public function generateShowtimes(Request $request)
    {
        $request->validate([
            'movie_id' => 'required|exists:movie,id',
            'screen_ids' => 'required|array',
            'screen_ids.*' => 'exists:screen,id',
            'start_date' => 'required|date',
            'days' => 'required|numeric|min:1|max:30',
            'base_price' => 'required|numeric|min:0',
        ]);

        try {
            $movie = Movie::find($request->movie_id);
            $screens = Screen::whereIn('id', $request->screen_ids)->get();
            $startDate = Carbon::parse($request->start_date);
            $days = $request->days;
            
            $createdShowtimes = [];
            $createdSeats = 0;

            foreach ($screens as $screen) {
                $seatCount = Seat::where('screen_id', $screen->id)->count();
                
                if ($seatCount == 0) {
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
                    $createdSeats += count($seats);
                }
            }

            // Các khung giờ chiếu trong ngày
            $timeSlots = [
                ['hour' => 9, 'minute' => 0],   // 9:00
                ['hour' => 13, 'minute' => 30], // 13:30
                ['hour' => 17, 'minute' => 0],  // 17:00
                ['hour' => 20, 'minute' => 30], // 20:30
            ];

            // Lặp qua từng ngày
            for ($day = 0; $day < $days; $day++) {
                $currentDate = $startDate->copy()->addDays($day);
                
                // Lặp qua từng phòng
                foreach ($screens as $screen) {
                    // Lặp qua từng khung giờ
                    foreach ($timeSlots as $slot) {
                        $startAt = $currentDate->copy()
                            ->setHour($slot['hour'])
                            ->setMinute($slot['minute'])
                            ->setSecond(0);
                        
                        $endAt = $startAt->copy()->addMinutes($movie->duration_min);

                        // Kiểm tra conflict
                        $conflict = Showtime::where('screen_id', $screen->id)
                            ->where(function ($query) use ($startAt, $endAt) {
                                $query->whereBetween('start_at', [$startAt, $endAt])
                                    ->orWhereBetween('end_at', [$startAt, $endAt])
                                    ->orWhere(function ($q) use ($startAt, $endAt) {
                                        $q->where('start_at', '<=', $startAt)
                                          ->where('end_at', '>=', $endAt);
                                    });
                            })
                            ->exists();

                        // Nếu không conflict thì tạo
                        if (!$conflict) {
                            $showtime = Showtime::create([
                                'movie_id' => $movie->id,
                                'screen_id' => $screen->id,
                                'start_at' => $startAt,
                                'end_at' => $endAt,
                                'base_price' => $request->base_price,
                                'status' => 'OPEN',
                            ]);
                            
                            $createdShowtimes[] = $showtime;
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Showtimes generated successfully',
                'data' => [
                    'movie' => $movie->title,
                    'screens' => $screens->pluck('name'),
                    'total_showtimes' => count($createdShowtimes),
                    'total_seats_created' => $createdSeats,
                    'showtimes' => $createdShowtimes,
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Generate showtimes failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
