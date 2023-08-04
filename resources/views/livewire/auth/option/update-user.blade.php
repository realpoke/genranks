<x-sections.form submit="updateUser">
    <x-slot name="title">
        {{ __('Profile Information') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Update your account\'s profile information.') }}
    </x-slot>

    <x-slot name="form">
        <!-- Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-inputs.label for="form.name" value="{{ __('Name') }}" />
            <x-inputs.text id="form.name" type="text" class="block w-full mt-1" wire:model="form.name" />
            <x-inputs.error for="form.name" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="col-span-6 sm:col-span-4">
            <x-inputs.label for="form.email" value="{{ __('Email') }}" />
            <x-inputs.text id="form.email" type="email" class="block w-full mt-1" wire:model="form.email" />
            <x-inputs.error for="form.email" class="mt-2" />
            @if (!$this->user->hasVerifiedEmail())
                <p wire:poll.10s class="mt-2 text-sm dark:text-white">
                    {{ __('Your email address is unverified.') }}

                    <x-buttons.main wire:click.prevent="sendEmailVerification">
                        {{ __('Click here to re-send the verification email.') }}
                    </x-buttons.main>
                </p>

                <x-sections.action-message class="mr-3" on="verification-link-sent">
                    <p class="mt-2 text-sm font-medium text-green-600 dark:text-green-400">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                </x-sections.action-message>
            @endif
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-sections.action-message class="mr-3" on="option-user-saved">
            {{ __('Saved.') }}
        </x-sections.action-message>

        <x-buttons.main wire:loading.attr="disabled">
            {{ __('Save') }}
        </x-buttons.main>
    </x-slot>
</x-sections.form>
