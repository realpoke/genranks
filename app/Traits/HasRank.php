<?php

namespace App\Traits;

trait HasRank
{
    public function adjustRanks(int $oldElo, int $newElo, bool $monthly = false): bool
    {
        if ($this->rank === null) {
            return $this->setInitialRank($newElo, $monthly);
        } elseif ($oldElo < $newElo) {
            return $this->rankUp($oldElo, $newElo, $monthly);
        } elseif ($oldElo > $newElo) {
            return $this->rankDown($oldElo, $newElo, $monthly);
        }

        return false;
    }

    private function setInitialRank(int $elo, bool $monthly): bool
    {
        $rankMethod = $this->getRankMethod($monthly);

        $usersToUpdate = $this->getUsersToUpdate(0, $elo, $monthly);
        $bestRank = $usersToUpdate->get()->min($rankMethod);

        if ($bestRank != null) {
            $this->$rankMethod = $bestRank;
            $usersToUpdate->increment($rankMethod);
        } else {
            $maxRank = self::where('id', '!=', $this->id)
                ->whereNotNull($rankMethod)
                ->max($rankMethod);
            $this->$rankMethod = $maxRank == null ? 1 : ($maxRank + 1);
        }

        return $this->save();
    }

    private function rankUp(int $oldElo, int $newElo, bool $monthly): bool
    {
        $rankMethod = $this->getRankMethod($monthly);

        $usersToUpdate = $this->getUsersToUpdate($oldElo, $newElo, $monthly);
        $usersToUpdate->increment($rankMethod);

        $this->$rankMethod = $this->$rankMethod - $usersToUpdate->get()->count();

        return $this->save();
    }

    private function rankDown(int $oldElo, int $newElo, bool $monthly): bool
    {
        $rankMethod = $this->getRankMethod($monthly);

        $usersToUpdate = $this->getUsersToUpdate($oldElo, $newElo, $monthly);
        $usersToUpdate->decrement($rankMethod);

        $this->$rankMethod = $this->$rankMethod + $usersToUpdate->get()->count();

        return $this->save();
    }

    private function getUsersToUpdate(int $oldElo, int $newElo, bool $monthly): \Illuminate\Database\Eloquent\Builder
    {
        $rankMethod = $this->getRankMethod($monthly);
        $eloMethod = $this->getEloMethod($monthly);

        if ($this->$rankMethod == null) {
            return self::where('id', '!=', $this->id)
                ->where($eloMethod, '<=', $newElo)
                ->whereNotNull($rankMethod);
        } elseif ($oldElo < $newElo) {
            return self::where('id', '!=', $this->id)
                ->where($rankMethod, '<', $this->$rankMethod)
                ->whereNotNull($rankMethod)
                ->whereBetween($eloMethod, [min($oldElo, $newElo), max($oldElo, $newElo)]);
        } elseif ($oldElo > $newElo) {
            return self::where('id', '!=', $this->id)
                ->where($rankMethod, '>', $this->$rankMethod)
                ->whereNotNull($rankMethod)
                ->whereBetween($eloMethod, [min($oldElo, $newElo), max($oldElo, $newElo)]);
        }

        return self::query();
    }

    private function getRankedMethod(bool $monthly): string
    {
        return $monthly ? 'monthly_rank' : 'rank';
    }

    private function getEloMethod(bool $monthly): string
    {
        return $monthly ? 'monthly_elo' : 'elo';
    }
}
