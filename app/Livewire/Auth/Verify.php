<?php

namespace App\Livewire\Auth;

use App\Contracts\Auth\LogoutUserContract;
use App\Contracts\Auth\SendsEmailVerificationContract;
use App\Traits\WithLimits;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Verify extends Component
{
    use WithLimits;

    public function resend(SendsEmailVerificationContract $sender)
    {
        $this->limitTo(2, 'sender', 'resend verification email');

        $sender->send();

        $this->dispatch('email-resent');
    }

    public function logout(LogoutUserContract $outlogger)
    {
        $outlogger->logout();
    }
}
