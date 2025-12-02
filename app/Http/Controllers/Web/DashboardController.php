<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
