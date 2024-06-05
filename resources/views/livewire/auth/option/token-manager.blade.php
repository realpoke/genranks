<div>
    <!-- Manage API Tokens -->
    <div class="mt-10 sm:mt-0">
        <x-sections.action>
            <x-slot name="title">
                {{ __('Manage API Tokens') }}
            </x-slot>

            <x-slot name="description">
                {{ __('You may delete any of your existing tokens if they are no longer needed.') }}
            </x-slot>

            <!-- API Token List -->
            <x-slot name="content">
                <div class="space-y-6">
                    <div>
                        {{ __('Delete your application tokens here. Useful for when you forgot to logout from the application.') }}
                    </div>

                    <x-sections.action-message on="api-token-deleted">
                        {{ __('Removed token.') }}
                    </x-sections.action-message>

                    @forelse (Auth::user()->tokens->sortBy('name') as $token)
                        <div class="flex items-center justify-between">
                            <div class="break-all dark:text-white">
                                {{ $token->name }}
                            </div>

                            <div class="flex items-center ml-2">
                                @if ($token->last_used_at)
                                    <div class="text-sm text-gray-400">
                                        {{ __('Last used') }} {{ $token->last_used_at->diffForHumans() }}
                                    </div>
                                @else
                                    <div class="text-sm text-gray-400">
                                        {{ __('Created at ') }} {{ $token->created_at->diffForHumans() }}
                                    </div>
                                @endif

                                <button class="ml-6 text-sm text-red-500 cursor-pointer"
                                    wire:click="confirmApiTokenDeletion({{ $token }})">
                                    {{ __('Delete') }}
                                </button>
                            </div>
                        </div>
                    @empty
                        <div>
                            {{ __('Once your you have used our desktop application you will see your tokens here.') }}
                        </div>
                    @endforelse
                </div>
            </x-slot>
        </x-sections.action>
    </div>

    <!-- Delete Token Confirmation Modal -->
    <x-modals.dialog wire:model="form.confirmingApiTokenDeletion">
        <x-slot name="title">
            {{ __('Delete API Token') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you would like to delete this API token?') }}
        </x-slot>

        <x-slot name="footer">
            <x-buttons.secondary wire:click="$toggle('form.confirmingApiTokenDeletion')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-buttons.secondary>

            <x-buttons.danger class="ml-3" wire:click="deleteApiToken" wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-buttons.danger>
        </x-slot>
    </x-modals.dialog>
</div>
