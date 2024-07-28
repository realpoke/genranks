@props(['disabled' => false, 'name' => null, 'id' => null])

@php
    $classes = $errors->has($name ?? $id)
        ? 'mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-red-900 ring-1 ring-inset ring-red-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6'
        : 'mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6';
@endphp

<select {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</select>
