<x-notifications.base :notification="$notification">
    This type of notification is not supported! Let us know on <a target="_blank" href="{{ route('discord') }}"
        class="inline-flex items-center font-semibold text-indigo-400 hover:text-indigo-300">Discord<x-icons
            icon="external-link" class="w-4 h-4" /></a> ({{ $notification->id }})
</x-notifications.base>
