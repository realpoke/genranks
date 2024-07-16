<div>
    <x-layouts.container>
        <x-sections.card>
            <ul role="list" class="divide-y divide-gray-200">
                @forelse ($maps as $map)
                    <li class="flex items-center justify-between py-4">
                        <span>
                            {{ $map->ranked ? 'Ranked' : 'Unranked' }} - {{ $map->name }} ({{ $map->hash }}) -
                            {{ $map->type }}
                        </span>
                        @if ($map->file)
                            <button wire:click="downloadMap({{ $map->id }})"
                                class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                                Download
                            </button>
                        @endif
                    </li>
                @empty
                    <li>No maps found.</li>
                @endforelse
            </ul>
        </x-sections.card>
    </x-layouts.container>
</div>
