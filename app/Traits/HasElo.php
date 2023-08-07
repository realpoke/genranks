<?php

namespace App\Traits;

trait HasElo
{
    public function giveElo(int $elo): bool
    {
        return $this->changeElo(abs($elo));
    }

    public function takeElo(int $elo): bool
    {
        return $this->changeElo(-abs($elo));
    }

    private function changeElo(int $changeElo): bool
    {
        $oldElo = $this->elo;
        $this->elo = $oldElo + $changeElo;

        $oldEloMonthly = $this->monthly_elo;
        $this->monthly_elo = $oldEloMonthly + $changeElo;

        return $this->save() &&
            $this->adjustRanks($oldElo, $this->elo) &&
            $this->adjustRanks($oldEloMonthly, $this->elo, true);
    }
}
