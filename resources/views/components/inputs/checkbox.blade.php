@props(['for'])

<div class="flex items-start">
    <div class="flex items-center h-5">
        <input type="checkbox" id="{{ $for }}" name="{{ $for }}" {!! $attributes->merge([
            'class' =>
                'w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-600 dark:ring-offset-gray-800',
        ]) !!}>
    </div>
    <div class="ml-2 text-sm">
        <x-inputs.label for="{{ $for }}">
            {{ $slot }}
        </x-inputs.label>
    </div>
</div>
