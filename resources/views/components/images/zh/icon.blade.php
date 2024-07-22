@php
    $type = '.png';
    $explodedIcon = collect(explode('_', $icon));
    $name = $explodedIcon->pop();
    $subFolder = $explodedIcon->pop();

    if (
        !is_null($subFolder) &&
        Storage::disk('images')->exists('zh' . $subFolder . '/' . $subFolder . '_' . $name . $type)
    ) {
        $url = Storage::disk('images')->url('zh' . $subFolder . '/' . $subFolder . '_' . $name . $type);
    } elseif (Storage::disk('images')->exists('zh/' . $name . $type)) {
        $url = Storage::disk('images')->url('zh/' . $name . $type);
    } else {
        $url = Storage::disk('images')->url('zh/unknown.png');
    }
@endphp

<img class="w-12 h-12 mx-auto" src="{{ $url }}" alt="{{ $icon }}" title="{{ $icon }}" />
