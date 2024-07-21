@php
    $type = '.png';
    $explodedIcon = collect(explode('_', $icon));
    $name = $explodedIcon->pop();
    $subFolder = $explodedIcon->pop();

    $imageDisk = Storage::disk('images');

    $filePath = 'zh/' . $name . $type;
    $fullPath = $imageDisk->path($filePath);

    if (!is_null($subFolder) && Storage::disk('images')->exists('zh' . $subFolder . '/' . $name . $type)) {
        $url = Storage::disk('images')->url('zh' . $subFolder . '/' . $name . $type);
    } elseif (Storage::disk('images')->exists('zh/' . $name . $type)) {
        $url = Storage::disk('images')->url('zh/' . $name . $type);
    } else {
        $url = Storage::disk('images')->url('zh/unknown.png');
    }
@endphp

<img class="w-12 h-12 mx-auto" src="{{ $url }}" alt="{{ $icon }}" title="{{ $icon }}" />
