<x-sections.form submit="updatePassword">
    <x-slot name="title">
        {{ __('Update Password') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Ensure your account is using a long, random password to stay secure.') }}
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6 sm:col-span-4">
            <x-inputs.label for="form.current_password" value="{{ __('Current Password') }}" />
            <x-inputs.text id="form.current_password" type="password" class="block w-full mt-1"
                wire:model="form.current_password" autocomplete="current-password" />
            <x-inputs.error for="form.current_password" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-inputs.label for="form.password" value="{{ __('New Password') }}" />
            <x-inputs.text id="form.password" type="password" class="block w-full mt-1" wire:model="form.password" />
            <x-inputs.error for="form.password" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-inputs.label for="form.password_confirmation" value="{{ __('Confirm Password') }}" />
            <x-inputs.text id="form.password_confirmation" type="password" class="block w-full mt-1"
                wire:model="form.password_confirmation" />
            <x-inputs.error for="form.password_confirmation" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-sections.action-message class="mr-3" on="option-password-saved">
            {{ __('Saved.') }}
        </x-sections.action-message>

        <x-buttons.main>
            {{ __('Save') }}
        </x-buttons.main>
    </x-slot>
</x-sections.form>
