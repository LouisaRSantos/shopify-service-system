<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (session('web_user_type') !== 'admin') {
            abort(403);
        }

        return $next($request);
    }
}