<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // ユーザーがログインしていない、または管理者でない場合
        if (!Auth::check() || Auth::user()->role !== 1) {
            return redirect('/admin/login');
        }

        return $next($request);
    }
}