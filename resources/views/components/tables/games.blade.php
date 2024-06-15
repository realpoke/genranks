<ul role="list" class="pt-4 divide-y divide-gray-100">
    @foreach ($games as $game)
        <li class="relative flex justify-between px-4 py-5 gap-x-6 hover:bg-gray-50 sm:px-6 lg:px-8">
            <div class="flex min-w-0 gap-x-4">
                <div class="flex-auto min-w-0">
                    <p class="text-sm font-semibold leading-6 text-gray-900">
                        <a wire:navigate href="{{ $game->route() }}">
                            <span class="absolute inset-x-0 bottom-0 -top-px"></span>
                            Blank
                        </a>
                    </p>
                    <p class="flex mt-1 text-xs leading-5 text-gray-500">
                        {{ $game->hash ?: 'Generating hash...' }}
                    </p>
                </div>
            </div>
            <div class="flex items-center shrink-0 gap-x-4">
                <div class="hidden sm:flex sm:flex-col sm:items-end">
                    <p class="text-sm leading-6 text-gray-900">{{ $game->created_at->diffForHumans() }}</p>
                    <div class="mt-1 flex items-center gap-x-1.5">
                        <a class="flex items-center" wire:navigate href="{{ $game->route() }}">
                            <div class="flex-none h-2 w-2 rounded-full {{ $game->status->classes() }}">
                                <div
                                    class="p-2 -ml-1 -mt-1 rounded-full {{ $game->status->animation() }} {{ $game->status->classes() }}">
                                </div>
                            </div>
                        </a>
                        <p class="text-xs leading-5 text-gray-500">{{ $game->status }}</p>
                    </div>
                </div>
                <svg class="flex-none w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                        clip-rule="evenodd" />
                </svg>
            </div>
        </li>
    @endforeach
</ul>
{{ $games->links('livewire/partials/paginate') }}
