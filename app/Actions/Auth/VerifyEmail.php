<?php

namespace App\Actions\Auth;

use App\Contracts\Auth\VerifyEmailContract;
use App\Livewire\Forms\Auth\EmailVerificationForm;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

class VerifyEmail implements VerifyEmailContract
{
    public function __invoke(EmailVerificationForm $form)
    {
        if (! hash_equals((string) $form->id, (string) $this->user()->getKey())) {
            throw new AuthorizationException();
        }

        if (! hash_equals((string) $form->hash, sha1($this->user()->getEmailForVerification()))) {
            throw new AuthorizationException();
        }

        if ($this->user()->hasVerifiedEmail()) {
            return redirect(route('home'));
        }

        if ($this->user()->markEmailAsVerified()) {
            event(new Verified($this->user()));
        }

        return redirect(route('home'));
    }

    #[Computed]
    private function user()
    {
        return Auth::user();
    }
}
