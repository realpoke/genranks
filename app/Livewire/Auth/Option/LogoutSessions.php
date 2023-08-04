<?php

namespace App\Livewire\Auth\Option;

use App\Contracts\Auth\Option\LogoutSessionsContract;
use App\Livewire\Forms\Auth\Option\LogoutSessionsForm;
use App\Traits\FormAttributes;
use App\Traits\withLimits;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use Livewire\Attributes\Computed;
use Livewire\Component;

class LogoutSessions extends Component
{
    use FormAttributes, withLimits;

    public LogoutSessionsForm $form;

    private LogoutSessionsContract $sessionManager;

    public function boot(LogoutSessionsContract $sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    public function confirmLogout()
    {
        $this->resetErrorBag();
        $this->form->password = '';

        $this->dispatch('confirming-logout-other-browser-sessions');
        $this->form->confirmingLogout = true;
    }

    public function logoutSessions()
    {
        $this->limitTo(10, 'form.password', 'logout other sessions');

        $this->sessionManager->logout($this->form);

        $this->dispatch('logged-out');
    }

    #[Computed]
    public function sessions()
    {
        if (! $this->sessionManager->isDatabaseSession()) {
            return collect();
        }

        return collect(
            DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
                ->where('user_id', Auth::user()->getAuthIdentifier())
                ->orderBy('last_activity', 'desc')
                ->get()
        )->map(function ($session) {
            return (object) [
                'agent' => $this->createAgent($session),
                'ip_address' => $session->ip_address,
                'is_current_device' => $session->id === request()->session()->getId(),
                'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
            ];
        });
    }

    private function createAgent($session)
    {
        return tap(new Agent, function ($agent) use ($session) {
            $agent->setUserAgent($session->user_agent);
        });
    }
}
