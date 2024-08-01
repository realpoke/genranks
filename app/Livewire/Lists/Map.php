<?php

namespace App\Livewire\Lists;

use App\Livewire\DynamicList;
use App\Models\Map as ModelsMap;
use App\Traits\WithLimits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;

class Map extends DynamicList
{
    use WithLimits;

    public function downloadMap($mapId)
    {
        $this->limitTo(3, 'map', 'download more maps');

        $map = ModelsMap::findOrFail($mapId);
        if (! $map->file || ! Storage::disk('maps')->fileExists($map->file)) {
            return;
        }

        return Storage::disk('maps')->download($map->file);
    }

    protected $itemView = 'items.maps';

    #[Computed()]
    protected function getModel(): Model
    {
        return new (ModelsMap::class);
    }
}
