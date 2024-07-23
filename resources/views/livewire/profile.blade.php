<x-layouts.container>
    <x-sections.card>
        <h1 class="text-4xl text-gray-900 dark:text-gray-100">{{ $user->name }}</h1>
        <p>Welcome to my profile!</p>

        <p>stats: {{ collect($user->stats) }}</p>

        <div> {{-- TODO: Make this a component --}}
            @foreach (collect($user->stats) as $stat => $value)
                @if (in_array($stat, ['UnitsCreated', 'BuildingsBuilt', 'UpgradesBuilt', 'PowersUsed']))
                    <div class="flex flex-col py-6">
                        <h3 class="text-4xl">{{ $stat }}</h3>
                    </div>
                    <div class="grid grid-cols-2 gap-1 sm:grid-cols-4">
                        @php
                            if ($stat == 'PowersUsed') {
                                $sortedValues = collect($value)->sortDesc();
                            } else {
                                $sortedValues = collect($value)->sortByDesc('TotalSpent');
                            }
                        @endphp
                        @foreach ($sortedValues as $unit => $details)
                            <div
                                class="relative flex items-center space-x-3 bg-white border border-gray-300 shadow-sm hover:border-gray-400">
                                <div class="flex-shrink-0">
                                    <div
                                        class="absolute inset-0 items-center content-center w-5 h-5 pointer-events-none -left-2 -top-2">
                                        <x-images.zh.team :unit="$unit" />
                                    </div>
                                    <x-images.zh.icon :icon="$unit" :alt="$unit" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $unit }}</p>
                                    <p class="text-sm text-gray-500 truncate">
                                        @if ($stat == 'PowersUsed')
                                            {{ 'Used ' . $details . ' times' }}
                                        @else
                                            {{ ($details['Count'] ?? '0') . ' worth ' . number_format($details['TotalSpent'] ?? 0) }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endforeach
        </div>
    </x-sections.card>
    <x-sections.card class="pt-12">
        <h3 class="text-2xl">My games:</h3>
        <x-tables.games :games="$games" />
    </x-sections.card>
</x-layouts.container>
