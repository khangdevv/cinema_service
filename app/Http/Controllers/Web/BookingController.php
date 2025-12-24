<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Showtime;
use App\Models\Seat;
use App\Models\SeatLock;
use App\Models\OrderLine;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        // Lấy tất cả phim đang active
        $movies = Movie::where('is_active', true)
            ->orderBy('title', 'asc')
            ->get();

        return view('booking.index', compact('movies'));
    }

    public function showMovieDetail($id)
    {
        $movie = Movie::where('is_active', true)
            ->with([
                'showtimes' => function ($query) {
                    $query->where('status', 'OPEN')
                        ->where('start_at', '>=', Carbon::now())
                        ->orderBy('start_at', 'asc')
                        ->with('screen');
                }
            ])
            ->findOrFail($id);

        // Group showtimes by date
        $showtimesByDate = $movie->showtimes->groupBy(function ($showtime) {
            return Carbon::parse($showtime->start_at)->format('Y-m-d');
        });

        return view('booking.movie-detail', compact('movie', 'showtimesByDate'));
    }

    public function seatMap($showtimeId)
    {
        $showtime = Showtime::with(['movie', 'screen.seats'])
            ->where('status', 'OPEN')
            ->findOrFail($showtimeId);

        // Get booked seats
        $bookedSeats = OrderLine::whereHas('order', function ($query) use ($showtimeId) {
            $query->where('showtime_id', $showtimeId)
                ->whereIn('status', ['PAID']);
        })
            ->where('item_type', 'TICKET')
            ->pluck('seat_id')
            ->toArray();

        // Get locked seats
        $lockedSeats = SeatLock::where('showtime_id', $showtimeId)
            ->where('expires_at', '>', Carbon::now())
            ->pluck('seat_id')
            ->toArray();

        // Group seats by row
        $seatsByRow = $showtime->screen->seats->groupBy('row_label')->sortKeys();

        return view('booking.seat-map', compact('showtime', 'seatsByRow', 'bookedSeats', 'lockedSeats'));
    }

    /**
     * Lock a seat when user selects it
     */
    public function lockSeat(Request $request)
    {
        $validated = $request->validate([
            'seat_id' => 'required|exists:seat,id',
            'showtime_id' => 'required|exists:showtime,id'
        ]);

        $seatId = $validated['seat_id'];
        $showtimeId = $validated['showtime_id'];
        $account = auth()->guard('web')->user();

        // Check if seat is already booked
        $isBooked = OrderLine::whereHas('order', function ($q) use ($showtimeId) {
            $q->where('showtime_id', $showtimeId)
                ->whereIn('status', ['PAID', 'INIT']);
        })->where('seat_id', $seatId)
            ->where('item_type', 'TICKET')
            ->exists();

        if ($isBooked) {
            return response()->json([
                'success' => false,
                'message' => 'Ghế này đã được đặt'
            ], 409);
        }

        // Check if seat is locked by another user
        $existingLock = SeatLock::where('showtime_id', $showtimeId)
            ->where('seat_id', $seatId)
            ->where('expires_at', '>', Carbon::now())
            ->where('account_id', '!=', $account->id)
            ->exists();

        if ($existingLock) {
            return response()->json([
                'success' => false,
                'message' => 'Ghế này đang được giữ bởi người khác'
            ], 409);
        }

        // Create or update lock (extends lock if user re-selects)
        SeatLock::updateOrCreate(
            [
                'showtime_id' => $showtimeId,
                'seat_id' => $seatId,
                'account_id' => $account->id
            ],
            [
                'expires_at' => Carbon::now()->addMinutes(10)
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Đã khóa ghế thành công',
            'expires_at' => Carbon::now()->addMinutes(10)->toIso8601String()
        ]);
    }

    /**
     * Unlock a seat when user deselects it
     */
    public function unlockSeat(Request $request)
    {
        $validated = $request->validate([
            'seat_id' => 'required|exists:seat,id',
            'showtime_id' => 'required|exists:showtime,id'
        ]);

        $account = auth()->guard('web')->user();

        // Only delete lock owned by current user
        $deleted = SeatLock::where('showtime_id', $validated['showtime_id'])
            ->where('seat_id', $validated['seat_id'])
            ->where('account_id', $account->id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => $deleted ? 'Đã mở khóa ghế' : 'Không tìm thấy lock'
        ]);
    }
}
