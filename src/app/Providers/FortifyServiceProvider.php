<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;
use App\Http\Responses\LoginResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use App\Http\Responses\RegisterResponse;
use Laravel\Fortify\Contracts\VerifyEmailResponse as VerifyEmailResponseContract;
use App\Http\Responses\VerifyEmailResponse;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use App\Http\Responses\LogoutResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\LoginRequest;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->app->bind(
            \Laravel\Fortify\Http\Requests\LoginRequest::class,
            \App\Http\Requests\LoginRequest::class
        );

        Fortify::registerView(fn() => view('auth.register'));
        Fortify::loginView(fn() => view('auth.login'));

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        Fortify::verifyEmailView(fn() => view('auth.verify_email'));
        Fortify::redirects('register', '/email/verify');
        Fortify::redirects('verify-email', '/attendance');
        Fortify::redirects('logout', '/login');

        Fortify::authenticateUsing(function (LoginRequest $request) {


            if (!Auth::attempt($request->only('email', 'password'))) {
                throw ValidationException::withMessages([
                    'login' => 'ログイン情報が登録されていません',
                ]);
            }

            $user = Auth::user();

            if ($request->login_type === 'admin' && $user->role != 1) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'login' => '管理者アカウントではありません',
                ]);
            }

            if ($request->login_type === 'staff' && $user->role != 0) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'login' => 'スタッフアカウントではありません',
                ]);
            }

            return $user;
        });

        RateLimiter::for('login', function (Request $request) {
            $key = Str::lower($request->input('email')).'|'.$request->ip();
            return Limit::perMinute(5)->by($key);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });


        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);
        $this->app->singleton(RegisterResponseContract::class, RegisterResponse::class);
        $this->app->singleton(VerifyEmailResponseContract::class, VerifyEmailResponse::class);
        $this->app->singleton(LogoutResponseContract::class, LogoutResponse::class);
    }
}