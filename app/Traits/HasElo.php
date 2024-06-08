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

        // TODO: Use database transaction to make sure rank and elo is in sync
        return $this->save() && $this->adjustRanks($oldElo, $this->elo);
    }

    private function adjustRanks(int $oldElo, int $newElo): bool
    {
        if ($this->rank === null) {
            return $this->setInitialRank($newElo);
        }

        if ($oldElo < $newElo) {
            return $this->rankUp($oldElo, $newElo);
        }

        if ($oldElo > $newElo) {
            return $this->rankDown($oldElo, $newElo);
        }

        return false;
    }

    private function setInitialRank(int $elo): bool
    {
        $usersToUpdate = $this->getUsersToUpdate(0, $elo);
        $bestRank = $usersToUpdate->min('rank');

        if ($bestRank !== null) {
            $this->rank = $bestRank;
            $usersToUpdate->increment('rank');
        } else {
            $maxRank = self::where('id', '!=', $this->id)
                ->whereNotNull('rank')
                ->max('rank');
            $this->rank = $maxRank === null ? 1 : ($maxRank + 1);
        }

        return $this->save();
    }

    private function rankUp(int $oldElo, int $newElo): bool
    {
        $usersToUpdate = $this->getUsersToUpdate($oldElo, $newElo);
        $usersToUpdate->increment('rank');

        $this->rank = $this->rank - $usersToUpdate->get()->count();

        return $this->save();
    }

    private function rankDown(int $oldElo, int $newElo): bool
    {

        $usersToUpdate = $this->getUsersToUpdate($oldElo, $newElo);
        $usersToUpdate->decrement('rank');

        $this->rank = $this->rank + $usersToUpdate->get()->count();

        return $this->save();
    }

    private function getUsersToUpdate(int $oldElo, int $newElo): \Illuminate\Database\Eloquent\Builder
    {
        $query = self::where('id', '!=', $this->id)
            ->whereNotNull('rank');

        if ($this->rank === null) {
            return $query->where('elo', '<=', $newElo);
        }

        $eloRange = [min($oldElo, $newElo), max($oldElo, $newElo)];

        if ($oldElo < $newElo) {
            return $query->where('rank', '<', $this->rank)
                ->whereBetween('elo', $eloRange);
        }

        if ($oldElo > $newElo) {
            return $query->where('rank', '>', $this->rank)
                ->whereBetween('elo', $eloRange);
        }

        return self::query();
    }
}
