<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('web')->user();
        
        if (!$user) {
            return redirect()->route('auth.login.form');
        }

        return view('dashboard', compact('user'));
    }

    public function myBookings()
    {
        $user = Auth::guard('web')->user();
        
        if (!$user) {
            return redirect()->route('auth.login.form');
        }

        // Lấy tất cả đơn hàng của user
        $orders = Order::where('account_id', $user->id)
            ->with(['order_lines.seat', 'order_lines.product', 'showtime.movie', 'showtime.screen'])
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('booking.my-bookings', compact('user', 'orders'));
    }
}
