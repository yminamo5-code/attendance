<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {

        $user = $request->user();

        $request->session()->forget('url.intended');
        
        // メール未認証
        if (! $user->hasVerifiedEmail()) {
            return redirect('/email/verify');
        }

        // 管理者
        if (auth()->user()->role == 1) {
            return redirect('/admin/attendance/list');
        }

        // スタッフ
        if (auth()->user()->role == 0) {
            return redirect('/attendance');
        }
    }
}
