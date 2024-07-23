<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $metaJson = json_encode('{
            "MapFile": "maps/tournament island",
            "MapCRC": "12BE477C",
            "MapSize": "130668",
            "Seed": "687679906",
            "C": "100",
            "SR": "0",
            "StartingCredits": "50000",
            "O": "N",
            "Players": [
              {
                "Type": "H",
                "Name": "palle",
                "IP": "0000000",
                "Port": "15874",
                "FT": "TT",
                "Color": "3",
                "Faction": "5",
                "StartingPosition": "2",
                "Team": "1",
                "Unknown": "1"
              },
              {
                "Type": "H",
                "Name": "bob",
                "IP": "0000000",
                "Port": "16001",
                "FT": "TT",
                "Color": "1",
                "Faction": "12",
                "StartingPosition": "3",
                "Team": "0",
                "Unknown": "1"
              }
            ]
          }');

        $playerJson = json_encode('{
          "Type": "H",
          "Name": "mula26300",
          "IP": "C0A80000",
          "Port": "15874",
          "FT": "TT",
          "Color": "3",
          "Faction": "5",
          "StartingPosition": "2",
          "Team": "1",
          "Unknown": "1"
        },
        {
          "Type": "H",
          "Name": "ganz86",
          "IP": "2836000",
          "Port": "16001",
          "FT": "TT",
          "Color": "1",
          "Faction": "12",
          "StartingPosition": "3",
          "Team": "0",
          "Unknown": "1"
        }');

        return [
            'meta' => $metaJson,
            'players' => $playerJson,
            'hash' => Hash::make(Str::random()),
        ];
    }
}
