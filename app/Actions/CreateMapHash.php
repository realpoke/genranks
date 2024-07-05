<?php

namespace App\Actions;

use App\Contracts\CreatesMapHashContract;

class CreateMapHash implements CreatesMapHashContract
{
    public function __invoke(
        string $mapFile,
        string $mapCRC,
        string $mapSize,
    ): string {
        return md5($mapFile.$mapCRC.$mapSize);
    }
}
