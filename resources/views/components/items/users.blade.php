<li class="relative flex justify-between px-4 py-5 gap-x-6 hover:bg-gray-50 sm:px-6 lg:px-8">
    <div class="flex min-w-0 gap-x-4">
        <div class="flex-auto min-w-0">
            <p class="flex text-sm font-semibold leading-6 text-gray-900">
                <a class="flex items-center" wire:navigate href="{{ $model->route() }}">
                    <span class="absolute inset-x-0 bottom-0 -top-px"></span>
                    <div class="items-center gap-x-1">
                        <img class="object-cover w-4 h-4 mr-1" href="{{ $model->route() }}"
                            src="{{ $model->badgeUrl() }}" />
                        <div class="">{{ $model->name }}</div>
                    </div>
                </a>
            </p>
            <p class="flex items-center mt-1 text-xs leading-5 text-gray-500">
                {{ $model->elo }}
                <span class="ml-1 text-xs font-light">{{ ucwords(strtolower($model->bracket()->name)) }}</span>
            </p>
        </div>
    </div>
    <div class="flex items-center shrink-0 gap-x-4">
        <p class="text-sm leading-6 text-gray-900">{{ $model->updated_at->diffForHumans() }}</p>
        <p class="text-xs leading-5 text-gray-500">{{ $model->rank ?? 'unranked' }}</p>
        <svg class="flex-none w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd"
                d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                clip-rule="evenodd" />
        </svg>
    </div>
</li>
