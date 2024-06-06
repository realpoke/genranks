<x-layouts.container>
    <x-sections.card>
        <h1 class="text-4xl text-gray-900 dark:text-gray-100">Games</h1>
        <p>Latest activity</p>
        <x-tables.games :games="$games">
        </x-tables.games>
    </x-sections.card>
</x-layouts.container>
