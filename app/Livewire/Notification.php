<?php

namespace App\Livewire;

use App\Livewire\Partials\NavigationMenu;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Notification extends Component
{
    use WithPagination;

    public function toggleRead(DatabaseNotification $notification)
    {
        if (! Auth::user()->can('update', $notification)) {
            return;
        }

        if ($notification->unread()) {
            $notification->markAsRead();
        } else {
            $notification->markAsUnread();
        }

        $this->dispatch('notification-marked-as-read', $this->user()->unreadNotifications()->count())->to(NavigationMenu::class);
    }

    public function delete(DatabaseNotification $notification)
    {
        if (! Auth::user()->can('delete', $notification)) {
            return;
        }

        $notification->delete();
        $this->dispatch('notification-marked-as-read', $this->user()->unreadNotifications()->count())->to(NavigationMenu::class);
    }

    #[Computed()]
    public function user()
    {
        return Auth::user();
    }

    public function render()
    {
        return view('livewire.notification',
            ['notifications' => $this->user()->notifications()->paginate(10)]
        );
    }
}
