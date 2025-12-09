<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Movie;
use App\Models\Order;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // DASHBOARD
    public function dashboard()
    {
        $totalUsers = Account::count();
        $totalMovies = Movie::count();
        $totalOrders = Order::count();
        $totalRevenue = Order::where('status', 'PAID')->sum('total_amount');

        $recentOrders = Order::with(['account', 'showtime.movie'])
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalMovies' => $totalMovies,
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'recentOrders' => $recentOrders,
        ]);
    }

    // STATISTICS
    public function statistics()
    {
        $totalRevenue = Order::where('status', 'PAID')->sum('total_amount');
        $totalOrders = Order::count();
        $paidOrders = Order::where('status', 'PAID')->count();
        $totalMovies = Movie::count();
        $totalUsers = Account::count();
        $activeMovies = Movie::where('is_active', true)->count();

        // Orders by status
        $ordersByStatus = Order::selectRaw('status, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('status')
            ->get();

        // Orders by channel
        $ordersByChannel = Order::selectRaw('channel, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('channel')
            ->get();

        // Orders by payment method
        $ordersByPayment = Order::selectRaw('payment_method, COUNT(*) as count, SUM(total_amount) as total')
            ->whereNotNull('payment_method')
            ->groupBy('payment_method')
            ->get();

        // Top movies by revenue
        $topMovies = DB::table('movie')
            ->leftJoin('showtime', 'movie.id', '=', 'showtime.movie_id')
            ->leftJoin('orders', function($join) {
                $join->on('showtime.id', '=', 'orders.showtime_id')
                     ->where('orders.status', '=', 'PAID');
            })
            ->select('movie.id', 'movie.title', 'movie.poster',
                DB::raw('COUNT(orders.id) as ticket_count'),
                DB::raw('COALESCE(SUM(orders.total_amount), 0) as revenue'))
            ->groupBy('movie.id', 'movie.title', 'movie.poster')
            ->orderBy('revenue', 'desc')
            ->limit(10)
            ->get();

        // Users by role
        $usersByRole = Account::selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->get();

        return view('admin.statistics', [
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'paidOrders' => $paidOrders,
            'totalMovies' => $totalMovies,
            'totalUsers' => $totalUsers,
            'activeMovies' => $activeMovies,
            'ordersByStatus' => $ordersByStatus,
            'ordersByChannel' => $ordersByChannel,
            'ordersByPayment' => $ordersByPayment,
            'topMovies' => $topMovies,
            'usersByRole' => $usersByRole,
        ]);
    }

    // USER MANAGEMENT
    public function usersList()
    {
        $users = Account::orderBy('id', 'desc')->paginate(10);
        return view('admin.users.list', ['users' => $users]);
    }

    public function userEdit($id)
    {
        $user = Account::findOrFail($id);
        return view('admin.users.edit', ['user' => $user]);
    }

    public function userUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:account,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:CUSTOMER,STAFF,ADMIN',
            'is_active' => 'boolean',
        ]);

        $user = Account::findOrFail($id);
        $validated['is_active'] = $request->boolean('is_active');
        $user->update($validated);

        return redirect()->route('admin.users.list')
            ->with('success', 'Cập nhật người dùng thành công');
    }

    public function userDelete($id)
    {
        $user = Account::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.list')
            ->with('success', 'Xóa người dùng thành công');
    }

    public function userToggleStatus($id)
    {
        $user = Account::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        return redirect()->route('admin.users.list')
            ->with('success', 'Cập nhật trạng thái người dùng thành công');
    }

    // MOVIE MANAGEMENT
    public function moviesList()
    {
        $movies = Movie::orderBy('id', 'desc')->paginate(10);
        return view('admin.movies.list', ['movies' => $movies]);
    }

    public function movieCreate()
    {
        return view('admin.movies.create');
    }

    public function movieStore(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'duration_min' => 'required|integer|min:1',
            'genre' => 'nullable|string|max:100',
            'rating_code' => 'nullable|string|max:10',
            'poster' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        Movie::create($validated);

        return redirect()->route('admin.movies.list')
            ->with('success', 'Tạo phim mới thành công');
    }

    public function movieEdit($id)
    {
        $movie = Movie::findOrFail($id);
        return view('admin.movies.edit', ['movie' => $movie]);
    }

    public function movieUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'duration_min' => 'required|integer|min:1',
            'genre' => 'nullable|string|max:100',
            'rating_code' => 'nullable|string|max:10',
            'poster' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $movie = Movie::findOrFail($id);
        $validated['is_active'] = $request->boolean('is_active');
        $movie->update($validated);

        return redirect()->route('admin.movies.list')
            ->with('success', 'Cập nhật phim thành công');
    }

    public function movieDelete($id)
    {
        $movie = Movie::findOrFail($id);
        $movie->delete();

        return redirect()->route('admin.movies.list')
            ->with('success', 'Xóa phim thành công');
    }

    public function movieToggleStatus($id)
    {
        $movie = Movie::findOrFail($id);
        $movie->is_active = !$movie->is_active;
        $movie->save();

        return redirect()->route('admin.movies.list')
            ->with('success', 'Cập nhật trạng thái phim thành công');
    }
}
