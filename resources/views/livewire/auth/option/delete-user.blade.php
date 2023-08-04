<x-cards.option>
    <x-slot name="title">
        {{ __('Delete Account') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Permanently delete your account.') }}
    </x-slot>

    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}

    <div class="mt-5">
        <x-buttons.danger wire:click="confirmUserDeletion" wire:loading.attr="disabled">
            {{ __('Delete Account') }}
        </x-buttons.danger>
    </div>

    <!-- Delete User Confirmation Modal -->
    <x-modals.dialog wire:model="form.confirmingUserDeletion">
        <x-slot name="title">
            {{ __('Delete Account') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}

            <div class="mt-4" x-data x-on:confirming-delete-user.window="setTimeout(() => $refs.password.focus(), 250)">
                <x-inputs.text type="password" class="block w-3/4 mt-1" placeholder="{{ __('Password') }}"
                    x-ref="password" wire:model="form.password" wire:keydown.enter="deleteUser" />

                <x-inputs.error for="form.password" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-buttons.secondary wire:click="$toggle('form.confirmingUserDeletion')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-buttons.secondary>

            <x-buttons.danger class="ml-3" wire:click.prevent="deleteUser" wire:loading.attr="disabled">
                {{ __('Delete Account') }}
            </x-buttons.danger>
        </x-slot>
    </x-modals.dialog>
</x-cards.option>
