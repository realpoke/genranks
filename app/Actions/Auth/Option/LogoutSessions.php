<?php

namespace App\Actions\Auth\Option;

use App\Contracts\Auth\Option\LogoutSessionsContract;
use App\Livewire\Forms\Auth\Option\LogoutSessionsForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogoutSessions implements LogoutSessionsContract
{
    public function logout(LogoutSessionsForm $form)
    {
        if (! $this->isDatabaseSession()) {
            return;
        }
        $form->validate();

        Auth::guard()->logoutOtherDevices($form->password);
        $this->deleteOtherSessionRecords();

        request()->session()->put([
            'password_hash_'.Auth::getDefaultDriver() => Auth::user()->getAuthPassword(),
        ]);

        $form->confirmingLogout = false;
    }

    public function isDatabaseSession(): bool
    {
        return config('session.driver') === 'database';
    }

    private function deleteOtherSessionRecords()
    {
        if (! $this->isDatabaseSession()) {
            return;
        }

        DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
            ->where('user_id', Auth::user()->getAuthIdentifier())
            ->where('id', '!=', request()->session()->getId())
            ->delete();
    }
}
