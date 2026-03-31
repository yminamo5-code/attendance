<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // 未ログインなら /login へ
        $middleware->redirectGuestsTo('/login');

        // ログイン済ユーザーのリダイレクト先を制御
        $middleware->redirectUsersTo(function ($request) {

            $user = $request->user();

            if (! $user->hasVerifiedEmail()) {
                return '/email/verify';
            }

            return '/attendance';
        });

    })
    ->withExceptions(function (Exceptions $exceptions): void {

    })->create();