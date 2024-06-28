<?php

namespace App\Actions;

use App\Contracts\CreatesMapHashContract;
use Illuminate\Support\Facades\Hash;

class CreateMapHash implements CreatesMapHashContract
{
    public function __invoke(
        string $mapFile,
        string $mapCRC,
        string $mapSize,
    ): string {
        return Hash::make($mapFile.$mapCRC.$mapSize);
    }
}
