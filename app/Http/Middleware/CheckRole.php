<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole {
    public function handle(Request $request, Closure $next, $role) {
        if (!$request->acccount()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        if ($request->account()->role !== $role) {
            return response()->json(['error', 'Unauthorized. Required role : ' .$role], 403);
        }

        return $next($request);
    }
}

