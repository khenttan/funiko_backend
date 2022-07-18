<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ActiveUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->status == "0") {
            $user = auth()->user();
            auth()->logout();
            return response()->json([
                'status' => 0,
                'message' => 'User Deactivate please contact to admin'
            ]);
        }
        return $next($request);
    }
}
