<x-sections.action>
    <x-slot name="title">{{ $title }}</x-slot>
    <x-slot name="description">{{ $description ?? '' }}</x-slot>
    <x-slot name="content">{{ $slot }}</x-slot>
</x-sections.action>
