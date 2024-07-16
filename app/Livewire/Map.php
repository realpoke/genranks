<?php

namespace App\Livewire;

use App\Models\Map as ModelsMap;
use App\Traits\WithLimits;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Map extends Component
{
    use WithLimits;

    public $maps;

    public function render()
    {
        $this->maps = ModelsMap::all();

        return view('livewire.map');
    }

    public function downloadMap($mapId)
    {
        $this->limitTo(3, 'map', 'download more maps');

        $map = ModelsMap::findOrFail($mapId);
        if (! $map->file || ! Storage::disk('maps')->fileExists($map->file)) {
            return;
        }

        return Storage::disk('maps')->download($map->file);
    }
}
