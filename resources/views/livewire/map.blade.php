<div>
    <x-layouts.container>
        <x-sections.card>
            <ul role="list" class="divide-y divide-gray-200">
                @forelse ($maps as $map)
                    <li class="py-4">
                        {{ $map->ranked ? 'Ranked' : 'Unranked' }} - {{ $map->name }} ({{ $map->hash }})
                    </li>
                @empty
                @endforelse
            </ul>
        </x-sections.card>
    </x-layouts.container>
</div>
