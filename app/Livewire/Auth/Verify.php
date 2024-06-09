<?php

namespace App\Livewire\Auth;

use App\Contracts\Auth\LogoutUserContract;
use App\Contracts\Auth\SendsEmailVerificationContract;
use App\Traits\withLimits;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Verify extends Component
{
    use withLimits;

    public function resend(SendsEmailVerificationContract $sender)
    {
        $this->limitTo(2, 'sender', 'resend verification email');

        $sender();

        $this->dispatch('email-resent');
    }

    public function logout(LogoutUserContract $outlogger)
    {
        $loggedOut = $outlogger();

        $this->redirectRoute('home', navigate: true);
    }
}
