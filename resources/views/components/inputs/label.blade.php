@props(['value', 'for' => null])

@php
    $classes = $errors->has($for) ? 'text-red-600 dark:text-red-400 block font-medium text-sm' : 'block font-medium text-sm text-gray-700 dark:text-gray-300';
@endphp

<label for="{{ $for }}" class="{{ $classes }}">
    {{ $value ?? $slot }}
</label>
