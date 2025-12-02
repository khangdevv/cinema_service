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
            ->with(['showtimes' => function($query) {
                $query->where('status', 'OPEN')
                    ->where('start_at', '>=', Carbon::now())
                    ->orderBy('start_at', 'asc')
                    ->with('screen');
            }])
            ->findOrFail($id);
        
        // Group showtimes by date
        $showtimesByDate = $movie->showtimes->groupBy(function($showtime) {
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
        $bookedSeats = OrderLine::whereHas('order', function($query) use ($showtimeId) {
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
}
