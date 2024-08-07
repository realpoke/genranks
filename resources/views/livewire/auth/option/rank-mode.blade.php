<x-sections.form submit="setRankMode">
    <x-slot name="title">
        {{ __('Ranked Mode') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Change what rank mode you want to play.') }}
    </x-slot>

    <x-slot name="form">
        <!-- Rank Mode -->
        <div class="col-span-6 sm:col-span-4">
            <x-inputs.label for="form.mode" value="{{ __('Rank Mode') }}" />
            <x-inputs.select id="form.mode" wire:model.live="form.mode">
                @foreach ($modes as $mode)
                    <option {{ $mode == $form->mode ? 'selected' : '' }} value="{{ $mode }}">
                        {{ ucfirst($mode) }}{{ $mode == 'balanced' ? ' (Premium)' : '' }}
                    </option>
                @endforeach
            </x-inputs.select>
            <x-inputs.error for="form.mode" class="mt-2" />
            <p class="mt-2 text-sm text-gray-600">
                {{ $modeText }}
            </p>
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-sections.action-message class="mr-3" on="rank-mode-saved">
            {{ __('Saved.') }}
        </x-sections.action-message>

        <x-buttons.main wire:loading.attr="disabled">
            {{ __('Save') }}
        </x-buttons.main>
    </x-slot>
</x-sections.form>
