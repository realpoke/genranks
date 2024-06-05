<?php

namespace App\Actions\Auth;

use App\Contracts\Auth\SendsEmailVerificationContract;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

class SendEmailVerification implements SendsEmailVerificationContract
{
    public function __invoke()
    {
        if ($this->user()->hasVerifiedEmail()) {
            return redirect(route('home'));
        }

        $this->user()->sendEmailVerificationNotification();
    }

    #[Computed]
    private function user()
    {
        return Auth::user();
    }
}
