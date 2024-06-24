<div class="overflow-hidden bg-gray-900 isolate">
    <div class="px-6 pt-24 mx-auto text-center max-w-7xl pb-96 sm:pt-32 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-base font-semibold leading-7 text-indigo-400">
                {{ collect(explode('/', $game->meta['MapFile']))->last() }}</h2>
            <div class="flex items-baseline space-x-4">
                <p class="flex-1 mt-2 text-4xl font-bold tracking-tight text-right text-white sm:text-5xl">
                    {{ $game->summary[0]['Name'] }}
                </p>
                <p class="mt-2 text-sm font-normal tracking-tight text-white sm:text-lg">
                    VS
                </p>
                <p class="flex-1 mt-2 text-4xl font-bold tracking-tight text-left text-white sm:text-5xl">
                    {{ $game->summary[1]['Name'] }}
                </p>
            </div>
        </div>
        <div class="relative mt-6">
            <p class="flex items-baseline justify-center max-w-2xl mx-auto text-lg leading-8 text-white/60">
                <x-icons icon="dollar-sign" class="h-3 -mr-1" />
                {{ number_format($game->meta['StartingCredits']) }}
                <x-icons icon="clock" class="h-3 ml-4 -mr-1" />
                {{ $time }}
            </p>
            <svg viewBox="0 0 1208 1024"
                class="absolute -top-10 left-1/2 -z-10 h-[64rem] -translate-x-1/2 [mask-image:radial-gradient(closest-side,white,transparent)] sm:-top-12 md:-top-20 lg:-top-12 xl:top-0">
                <ellipse cx="604" cy="512" fill="url(#6d1bd035-0dd1-437e-93fa-59d316231eb0)" rx="604"
                    ry="512" />
                <defs>
                    <radialGradient id="6d1bd035-0dd1-437e-93fa-59d316231eb0">
                        <stop stop-color="#7775D6" />
                        <stop offset="1" stop-color="#E935C1" />
                    </radialGradient>
                </defs>
            </svg>
        </div>
    </div>
    <div class="flow-root pb-24 bg-white sm:pb-32">
        <div class="-mt-80">
            <div class="px-6 mx-auto max-w-7xl lg:px-8">
                <div class="grid max-w-md grid-cols-1 gap-8 mx-auto lg:max-w-4xl lg:grid-cols-2">
                    <div
                        class="{{ $game->status->value == 'valid' && $game->summary[0]['Win'] == true ? 'border-l-2 border-r-2 border-indigo-600' : '' }} flex flex-col justify-between p-8 bg-white shadow-xl rounded-3xl ring-1 ring-gray-900/10 sm:p-10">
                        <div>
                            <h3 id="tier-hobby"
                                class="flex items-center text-base font-semibold leading-7 text-indigo-600">
                                @if ($game->status->value == 'valid' && $game->summary[0]['Win'] == true)
                                    <x-icons icon="award" />
                                @endif
                                {{ $game->summary[0]['Side'] }}
                            </h3>

                            @if ($game->status->value == 'valid')
                                <div class="flex items-baseline mt-4 gap-x-2">
                                    <span
                                        class="text-5xl font-bold tracking-tight text-gray-900">{{ abs($users->first()->pivot->elo_change) }}</span>
                                    <div>
                                        @if ($game->summary[0]['Win'])
                                            <span class="text-base font-semibold leading-7 text-gray-600"><x-icons
                                                    class="text-green-500" icon="trend-up" /></span>
                                        @else
                                            <span class="text-base font-semibold leading-7 text-gray-600"><x-icons
                                                    class="text-red-500" icon="trend-down" /></span>
                                        @endif
                                        <p>/elo</p>
                                    </div>
                                </div>
                            @endif
                            <p class="mt-2 text-xs text-gray-600">
                                {{ $game->summary[0]['Name'] }}<br>

                                @if ($game->status->value == 'valid')
                                    Rank: {{ $users->first()->rank }}/{{ $users->first()->elo }}
                                @endif
                            </p>
                            <ul role="list" class="mt-10 space-y-4 text-sm leading-6 text-gray-600">
                                <li class="flex flex-col gap-y-3">
                                    <div class="font-bold">Overall</div>
                                    <!-- TODO: Add K/D ratio and CASH/MINUTE ratio -->
                                    <div class="flex items-center gap-x-3">
                                        <x-icons class="text-gray-600" icon="activity" />
                                        Total Spent: {{ number_format($game->summary[0]['MoneySpent']) }}
                                    </div>
                                </li>
                                @foreach ($categories as $category)
                                    <li class="flex flex-col gap-y-3">
                                        <div class="font-bold">{{ $category }}</div>

                                        @if ($category != 'PowersUsed')
                                            <div class="flex items-center gap-x-3">
                                                @if ($comparisonData[$category]['totalSpentPercentage'] > 25)
                                                    <x-icons class="text-red-500" icon="arrow-down-circle" />
                                                @elseif ($comparisonData[$category]['totalSpentPercentage'] < -25)
                                                    <x-icons class="text-green-500" icon="arrow-up-circle" />
                                                @else
                                                    <x-icons class="text-gray-600" icon="activity" />
                                                @endif
                                                Total Spent:
                                                {{ number_format($comparisonData[$category]['firstTotalSpent']) }}
                                            </div>
                                        @endif

                                        <div class="flex items-center gap-x-3">
                                            @if ($comparisonData[$category]['totalCountPercentage'] > 25)
                                                <x-icons class="text-red-500" icon="arrow-down-circle" />
                                            @elseif ($comparisonData[$category]['totalCountPercentage'] < -25)
                                                <x-icons class="text-green-500" icon="arrow-up-circle" />
                                            @else
                                                <x-icons class="text-gray-600" icon="activity" />
                                            @endif
                                            Total Count:
                                            {{ number_format($comparisonData[$category]['firstTotalCount']) }}
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div
                        class="{{ $game->status->value == 'valid' && $game->summary[1]['Win'] == true ? 'border-l-2 border-r-2 border-indigo-600' : '' }} flex flex-col justify-between p-8 bg-white shadow-xl rounded-3xl ring-1 ring-gray-900/10 sm:p-10">
                        <div>

                            <h3 id="tier-team"
                                class="flex items-center text-base font-semibold leading-7 text-indigo-600">
                                @if ($game->status->value == 'valid' && $game->summary[1]['Win'] == true)
                                    <x-icons icon="award" />
                                @endif
                                {{ $game->summary[1]['Side'] }}
                            </h3>
                            @if ($game->status->value == 'valid')
                                <div class="flex items-baseline mt-4 gap-x-2">
                                    <span
                                        class="text-5xl font-bold tracking-tight text-gray-900">{{ abs($users->last()->pivot->elo_change) }}</span>
                                    <div>
                                        @if ($game->summary[1]['Win'])
                                            <span class="text-base font-semibold leading-7 text-gray-600"><x-icons
                                                    class="text-green-500" icon="trend-up" /></span>
                                        @else
                                            <span class="text-base font-semibold leading-7 text-gray-600"><x-icons
                                                    class="text-red-500" icon="trend-down" /></span>
                                        @endif
                                        <p>/elo</p>
                                    </div>
                                </div>
                            @endif
                            <p class="mt-2 text-xs text-gray-600">
                                {{ $game->summary[1]['Name'] }}<br>

                                @if ($game->status->value == 'valid')
                                    Rank: {{ $users->last()->rank }}/{{ $users->last()->elo }}
                                @endif
                            </p>
                            <ul role="list" class="mt-10 space-y-4 text-sm leading-6 text-gray-600">
                                <li class="flex flex-col gap-y-3">
                                    <div class="font-bold">Overall</div>

                                    <div class="flex items-center gap-x-3">
                                        <x-icons class="text-gray-600" icon="activity" />
                                        Total Spent: {{ number_format($game->summary[1]['MoneySpent']) }}
                                    </div>
                                </li>
                                @foreach ($categories as $category)
                                    <li class="flex flex-col gap-y-3">
                                        <div class="font-bold">{{ $category }}</div>

                                        @if ($category != 'PowersUsed')
                                            <div class="flex items-center gap-x-3">
                                                @if ($comparisonData[$category]['totalSpentPercentage'] < -25)
                                                    <x-icons class="text-red-500" icon="arrow-down-circle" />
                                                @elseif ($comparisonData[$category]['totalSpentPercentage'] > 25)
                                                    <x-icons class="text-green-500" icon="arrow-up-circle" />
                                                @else
                                                    <x-icons class="text-gray-600" icon="activity" />
                                                @endif
                                                Total Spent:
                                                {{ number_format($comparisonData[$category]['secondTotalSpent']) }}
                                            </div>
                                        @endif

                                        <div class="flex items-center gap-x-3">
                                            @if ($comparisonData[$category]['totalCountPercentage'] < -25)
                                                <x-icons class="text-red-500" icon="arrow-down-circle" />
                                            @elseif ($comparisonData[$category]['totalCountPercentage'] > 25)
                                                <x-icons class="text-green-500" icon="arrow-up-circle" />
                                            @else
                                                <x-icons class="text-gray-600" icon="activity" />
                                            @endif
                                            Total Count:
                                            {{ number_format($comparisonData[$category]['secondTotalCount']) }}
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div
                        class="flex flex-col items-start p-8 gap-x-8 gap-y-6 rounded-3xl ring-1 ring-gray-900/10 sm:gap-y-10 sm:p-10 lg:col-span-2 lg:flex-row lg:items-center">
                        <div class="lg:min-w-0 lg:flex-1">
                            <h3 class="text-lg font-semibold leading-8 tracking-tight text-indigo-600">Something wrong?
                            </h3>
                            <p class="mt-1 text-base leading-7 text-gray-600">If you encounter any issues or bugs
                                while using the leaderboard, please contact our support team. Provide as
                                much detail as possible to help us
                                resolve the issue promptly.</p>

                            <p class="mt-2 -mb-3 text-xs text-gray-400">{{ $game->hash }}</p>
                        </div>
                        <a href="{{ route('discord') }}"
                            class="rounded-md px-3.5 py-2 text-sm font-semibold leading-6 text-indigo-600 ring-1 ring-inset ring-indigo-200 hover:ring-indigo-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Discord
                            Support</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
