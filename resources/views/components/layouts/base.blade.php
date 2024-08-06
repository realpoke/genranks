<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Tallest') }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css'])
    @paddleJS
</head>

<body class="font-sans antialiased">
    {{ $slot }}

    @stack('modals')

    <div class="fixed inset-x-0 bottom-0 pointer-events-none sm:flex sm:justify-center sm:px-6 sm:pb-5 lg:px-8">
        <div
            class="pointer-events-auto flex items-center justify-between gap-x-6 bg-gray-900 px-6 py-2.5 sm:rounded-xl sm:py-3 sm:pl-4 sm:pr-3.5">
            <p class="text-sm leading-6 text-white">
                <strong class="font-semibold">ALPHA</strong><svg viewBox="0 0 2 2"
                    class="mx-2 inline h-0.5 w-0.5 fill-current" aria-hidden="true">
                    <circle cx="1" cy="1" r="1" />
                </svg>GenRanks is currently in alpha, data wipes are possible at any time. Questions? Reach out on
                <a target="_blank" href="{{ route('discord') }}"
                    class="inline-flex items-center font-semibold text-indigo-400 hover:text-indigo-300">Discord<x-icons
                        icon="external-link" class="w-4 h-4" /></a>
                </a>
            </p>
        </div>
    </div>

</body>

</html>
