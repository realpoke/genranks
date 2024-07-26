<x-sections.card
    class="{{ $notification->unread() ? 'border-l-4' : '' }} border-yellow-400 rounded-md dark:yellow-indigo-600">
    <div class="flex items-center justify-between">
        <div>
            {{ $slot ?? $notification->data['message'] }}
        </div>
        <div>
            <x-buttons.main wire:click="toggleRead('{{ $notification->id }}')">
                @if ($notification->unread())
                    Mark as read
                @else
                    Mark as unread
                @endif
            </x-buttons.main>
            <x-buttons.danger wire:click="delete('{{ $notification->id }}')">
                Delete
            </x-buttons.danger>
        </div>
    </div>
</x-sections.card>
