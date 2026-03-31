<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {

            if (str_starts_with($request->path(), 'admin')) {
                return '/admin/login';
            }

            return '/login';
        }
    }
}