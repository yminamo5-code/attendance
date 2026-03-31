<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        // role判定
        if (auth()->user()->role != $role) {
            abort(403); // 権限なし
        }

        return $next($request);
    }
}
