<?php

namespace App\Contracts;

interface CreatesMapHashContract
{
    public function __invoke(
        string $mapFile,
        string $mapCRC,
        string $mapSize,
    ): string;
}
