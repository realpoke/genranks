<x-layouts.container>
    <x-sections.card>
        <div class="flex justify-between">
            <h1 class="text-4xl text-gray-900 dark:text-gray-100">Blank</h1>
            <div class="mt-1 flex items-center gap-x-1.5">
                <div class="flex-none h-2 w-2 rounded-full {{ $game->status->classes() }}">
                    <div
                        class="p-2 -ml-1 -mt-1 rounded-full {{ $game->status->animation() }} {{ $game->status->classes() }}">
                    </div>
                </div>
                <p class="text-xs leading-5 text-gray-500">{{ $game->status }}</p>
            </div>
        </div>
        <p class="flex mt-1 text-xs leading-5 text-gray-500">
            {{ $game->hash ?: 'Generating hash...' }}
        </p>
    </x-sections.card>

    <x-sections.card class="pt-14">
        <p>TEST</p>
    </x-sections.card>
</x-layouts.container>
