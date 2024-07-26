<?php

namespace App\Policies;

use App\Models\User;
use Auth;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Notifications\DatabaseNotification;

class NotificationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any notifications.
     */
    public function viewAny(User $user): bool
    {
        // Any authenticated user can view their notifications
        return Auth::check() && $user->can('viewAny:notification');
    }

    /**
     * Determine whether the user can view the notification.
     */
    public function view(User $user, DatabaseNotification $databaseNotification): bool
    {
        // User can view the notification if it belongs to them
        return $user->can('view:notification')
            && $databaseNotification->notifiable_id === $user->id
            && $databaseNotification->notifiable_type === User::class;
    }

    /**
     * Determine whether the user can create notifications.
     */
    public function create(User $user): bool
    {
        // Typically, users cannot create notifications themselves
        return false;
    }

    /**
     * Determine whether the user can update the notification.
     */
    public function update(User $user, DatabaseNotification $databaseNotification): bool
    {
        // User can update (mark as read) the notification if it belongs to them
        return $user->can('update:notification')
            && $databaseNotification->notifiable_id === $user->id
            && $databaseNotification->notifiable_type === User::class;
    }

    /**
     * Determine whether the user can delete the notification.
     */
    public function delete(User $user, DatabaseNotification $databaseNotification): bool
    {
        // User can delete the notification if it belongs to them
        return $user->can('delete:notification')
            && $databaseNotification->notifiable_id === $user->id
            && $databaseNotification->notifiable_type === User::class;
    }

    /**
     * Determine whether the user can restore the notification.
     */
    public function restore(User $user, DatabaseNotification $databaseNotification): bool
    {
        // Not typically applicable to notifications, but ensuring it belongs to the user
        return $user->can('restore:notification')
            && $databaseNotification->notifiable_id === $user->id
            && $databaseNotification->notifiable_type === User::class;
    }

    /**
     * Determine whether the user can permanently delete the notification.
     */
    public function forceDelete(User $user, DatabaseNotification $databaseNotification): bool
    {
        // User can permanently delete the notification if it belongs to them
        return $user->can('forceDelete:notification')
            && $databaseNotification->notifiable_id === $user->id
            && $databaseNotification->notifiable_type === User::class;
    }
}
