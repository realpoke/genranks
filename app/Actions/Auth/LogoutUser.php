<?php

namespace App\Actions\Auth;

use App\Contracts\Auth\LogoutUserContract;
use Illuminate\Support\Facades\Auth;

class LogoutUser implements LogoutUserContract
{
    public function __invoke()
    {
        Auth::logout();

        if (request()->hasSession()) {
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }

        return true;
    }
}
