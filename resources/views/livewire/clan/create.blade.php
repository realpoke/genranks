<x-sections.form submit="createClan">
    <x-slot name="title">
        {{ __('Create Clan') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Create a clan to share your games with other players.') }}
    </x-slot>

    <x-slot name="form">
        <!-- Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-inputs.label for="form.name" value="{{ __('Name') }}" />
            <x-inputs.text id="form.name" type="text" class="block w-full mt-1" wire:model.live="form.name" />
            <x-inputs.error for="form.name" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-inputs.label for="form.tag" value="{{ __('Tag') }}" />
            <x-inputs.text id="form.tag" type="text" class="block w-full mt-1" wire:model.live="form.tag" />
            <x-inputs.error for="form.tag" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-inputs.label for="form.description" value="{{ __('Description') }}" />
            <x-inputs.textarea id="form.description" type="text" class="block w-full mt-1"
                wire:model.live="form.description" />
            <x-inputs.error for="form.description" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-buttons.main wire:loading.attr="disabled">
            {{ __('Create Clan') }}
        </x-buttons.main>
    </x-slot>
</x-sections.form>
