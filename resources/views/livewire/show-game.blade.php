<x-layouts.container>
    <x-sections.card>
        <div class="flex justify-between">
            <h1 class="text-4xl text-gray-900 dark:text-gray-100">{{ $game->players[0]['Name'] ?? 'UNKNOWN' }} VS
                {{ $game->players[1]['Name'] ?? 'UNKNOWN' }}</h1>
            <div class="mt-1 flex items-center gap-x-1.5">
                <div class="flex-none h-2 w-2 rounded-full {{ $game->status->classes() }}">
                    <div
                        class="p-2 -ml-1 -mt-1 rounded-full {{ $game->status->animation() }} {{ $game->status->classes() }}">
                    </div>
                </div>
                <p class="text-xs leading-5 text-gray-500">{{ $game->status }}</p>
            </div>
        </div>
        <p class="flex justify-between mt-1 text-xs leading-5 text-gray-500">
            <span class="font-extrabold">{{ collect(explode('/', $game->meta['MapFile']))->last() }}</span>
            <span>{{ $game->hash ?: 'Generating hash...' }}</span>
        </p>
    </x-sections.card>

    <x-sections.card class="pt-14">
        <ul class="space-y-20">
            @foreach ($game->summary as $summary)
                <li>{{ collect($summary) }}</li>
            @endforeach
        </ul>
    </x-sections.card>
</x-layouts.container>
