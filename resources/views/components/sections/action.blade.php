<div {{ $attributes->merge(['class' => 'md:grid md:grid-cols-3 md:gap-6 text-gray-600 dark:text-gray-400']) }}>
    <x-sections.title>
        <x-slot name="title">{{ $title }}</x-slot>
        <x-slot name="description">{{ $description }}</x-slot>
    </x-sections.title>

    <x-sections.card>
        {{ $content }}
    </x-sections.card>
</div>
