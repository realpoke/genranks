<?php

namespace Database\Seeders;

use App\Actions\CreateMapHash;
use App\Models\Map;
use Illuminate\Database\Seeder;

class MapHashSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Adding map hashes');

        $hasher = new CreateMapHash();
        $maps = [
            //'map name' => ['map crc', 'map size'],
            '[rank] twister land zh v1' => ['BA1F5028', '205670'],
            'tournament desert' => ['15F63DEA', '144534'],
            '[rank] drallim desert zh v2' => ['937B94F3', '77457'],
            'tournament island' => ['12BE477C', '130668'],
            '[rank] barren badlands balanced zh v2' => ['A0DCE7AD', '194291'],
            '[rank] snowy drought v4' => ['2199A64E', '143274'],
            '[rank] deserted village v3' => ['C5E31508', '193548'],
            '[rank] mountain mayhem v2' => ['5EE6A116', '107791'],
            '[rank] australia zh v1' => ['DB06F68', '177212'],
            '[rank] make make 2 zh v4' => ['D62A442F', '289492'],
            '[rank] sand scorpion' => ['FFFE2DB4', '164094'],
            '[rank] highway 99 zh v2' => ['58BABCB3', '160061'],
            'tournament c v1' => ['DE7B9904', '186879'],
            '[rank] td classic nocars zh v1' => ['632D0C7A', '144181'],
        ];

        foreach ($maps as $mapName => $mapMeta) {
            $this->command->info("Adding map: {$mapName}");
            $map = Map::firstOrCreate([
                'name' => $mapName,
                'hash' => $hasher($mapName, $mapMeta[0], $mapMeta[1]),
            ]);

            $this->command->info("Map: {$mapName} added");
            $map->save();
        }
    }
}
