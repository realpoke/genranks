@php
    $type = '.png';
    $explodedUnit = collect(explode('_', $unit));
    $unit = $explodedUnit->pop();
    $team = $explodedUnit->pop();

    if (Storage::disk('images')->exists('zh/Teams/' . $team . $type)) {
        $url = Storage::disk('images')->url('zh/Teams/' . $team . $type);
    } elseif (Str::contains($unit, 'GLA')) {
        $url = Storage::disk('images')->url('zh/Teams/GLA' . $type);
    } elseif (Str::contains($unit, 'China')) {
        $url = Storage::disk('images')->url('zh/Teams/China' . $type);
    } elseif (Str::contains($unit, 'America')) {
        $url = Storage::disk('images')->url('zh/Teams/America' . $type);
    }
@endphp

@if (!isset($url))
    <x-icons icon="help-circle" class="object-fill text-black" />
@else
    <img class="object-fill" src="{{ $url }}" alt="{{ $team ?? $unit }}" title="{{ $team ?? $unit }}" />
@endif
