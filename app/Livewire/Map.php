<?php

namespace App\Livewire;

use App\Models\Map as ModelsMap;
use Livewire\Component;

class Map extends Component
{
    public $maps;

    public function render()
    {
        $this->maps = ModelsMap::all();

        return view('livewire.map');
    }

    public function downloadMap($mapId)
    {
        $map = ModelsMap::findOrFail($mapId);
        $downloadUrl = $map->downloadURL();
        dd($map->downloadURL());

        if ($downloadUrl) {
            $this->redirect($downloadUrl);
        }
    }
}
