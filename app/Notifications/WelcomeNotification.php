<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function databaseType(): string
    {
        return 'base';
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => 'Welcome to GenRanks! 🎉 start your journey now by downloading GenLink.',
        ];
    }
}
