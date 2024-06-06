<x-layouts.container>
    <x-sections.card>
        <h1 class="text-4xl text-gray-900 dark:text-gray-100">{{ $user->name }}</h1>
        <p>Welcome to my profile!</p>


    </x-sections.card>
    <x-sections.card class="pt-12">
        <h3 class="text-2xl">My games:</h3>
        <x-tables.games :games="$games">
        </x-tables.games>
    </x-sections.card>
</x-layouts.container>
