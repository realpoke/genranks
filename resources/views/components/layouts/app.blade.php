<x-layouts.base>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        @livewire('partials.navigation-menu')

        <main>
            {{ $slot }}
        </main>
    </div>
</x-layouts.base>
