<x-layouts.container>
    <x-sections.card>
        <h1 class="text-4xl text-gray-900 dark:text-gray-100">Leaderboard</h1>
        <p>Best players</p>
        <x-tables.users :users="$users">
        </x-tables.users>
    </x-sections.card>
</x-layouts.container>
