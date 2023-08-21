<x-layouts.container>
    <x-sections.card>
        <h1 class="text-4xl">Landing Page</h1>
        <p>Welcome to a brand new idea!</p>
        <livewire:countdown-timer :target-date-time="$targetTime" :hasError="$error" />
        <livewire:dynamic-table :model="\App\Models\User::class" />
    </x-sections.card>
</x-layouts.container>
