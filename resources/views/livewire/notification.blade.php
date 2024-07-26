<x-layouts.container>
    <div class="space-y-4">
        @forelse ($notifications as $notification)
            @includeFirst(
                ['components.notifications.' . $notification->type, 'components.notifications.missing'],
                ['notification' => $notification]
            )
        @empty
            No notifications
        @endforelse
    </div>

    {{ $notifications->links() }}
</x-layouts.container>
