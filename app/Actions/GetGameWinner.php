<?php

namespace App\Actions;

use App\Contracts\GetsGameWinnerContract;
use App\Models\Game;
use Illuminate\Support\Collection;

class GetGameWinner implements GetsGameWinnerContract
{
    // TODO: Fix for any team sizes
    // Currently only works for 1v1
    public function winner(Game $game): Collection
    {
        $data = $game->data;

        if ($this->CheckMismatch($data)) {
            return collect();
        }

        if (! $this->checkWinnerTag($data)) {
            return collect();
        }

        return collect();
    }

    private function checkWinnerTag(array $data): ?int
    {
        if ($data['Summary'][0]['Win'] === true && $data['Summary'][1]['Win'] === false) {
            return 1;
        }
        if ($data['Summary'][0]['Win'] === false && $data['Summary'][1]['Win'] === true) {
            return 2;
        }

        return false;
    }

    private function CheckMismatch(array $data): bool
    {
        if ($data['Summary'][0]['Win'] === true && $data['Summary'][1]['Win'] === true) {
            return true;
        }

        return false;
    }
}
