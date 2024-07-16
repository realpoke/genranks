<?php

namespace App\Traits;

use App\Enums\EloRankType;
use App\Models\Game;

trait HasElo
{
    public function newElo(
        int $newElo,
        ?Game $game = null,
        EloRankType $rankType = EloRankType::ALL
    ): bool {
        if ($newElo == $this->{$rankType->databaseEloField()}) {
            return true;
        }

        if ($newElo > $this->{$rankType->databaseEloField()}) {
            return $this->giveElo($newElo - $this->{$rankType->databaseEloField()}, $game, $rankType);
        }

        if ($newElo < $this->{$rankType->databaseEloField()}) {
            return $this->takeElo($this->{$rankType->databaseEloField()} - $newElo, $game, $rankType);
        }

        return false;
    }

    public function giveElo(int $elo, ?Game $game = null, EloRankType $rankType = EloRankType::ALL): bool
    {
        return $this->changeElo(abs($elo), $game, $rankType);
    }

    public function takeElo(int $elo, ?Game $game = null, EloRankType $rankType = EloRankType::ALL): bool
    {
        return $this->changeElo(-abs($elo), $game, $rankType);
    }

    public function changeElo(int $changeElo, ?Game $game = null, EloRankType $rankType = EloRankType::ALL): bool
    {
        if (! is_null($game)) {
            $this->games()->updateExistingPivot($game->id, ['elo_change' => $changeElo]);
        }
        $oldElo = $this->{$rankType->databaseEloField()};
        $this->{$rankType->databaseEloField()} = max(0, $oldElo + $changeElo);

        // TODO: Use database transaction to make sure rank and elo is in sync
        return $this->save() && $this->adjustRanks($oldElo, $this->{$rankType->databaseEloField()}, $rankType);
    }

    private function adjustRanks(int $oldElo, int $newElo, EloRankType $rankType): bool
    {
        // TODO: Adjusting ranks are messing up the rankings many users have the same rank and there are gaps in the ranks
        if ($this->{$rankType->databaseRankField()} === null) {
            return $this->setInitialRank($newElo, $rankType);
        }

        if ($oldElo < $newElo) {
            return $this->rankUp($oldElo, $newElo, $rankType);
        }

        if ($oldElo > $newElo) {
            return $this->rankDown($oldElo, $newElo, $rankType);
        }

        return false;
    }

    private function setInitialRank(int $elo, EloRankType $rankType): bool
    {
        $usersToUpdate = $this->queryUsersToUpdate(0, $elo, $rankType);
        $bestRank = $usersToUpdate->min($rankType->databaseRankField());

        if ($bestRank !== null) {
            $this->rank = $bestRank;
            $usersToUpdate->increment($rankType->databaseRankField());
        } else {
            $maxRank = self::where('id', '!=', $this->id)
                ->ranked($rankType)
                ->max($rankType->databaseRankField());
            $this->{$rankType->databaseRankField()} = $maxRank === null ? 1 : ($maxRank + 1);
        }

        return $this->save();
    }

    private function rankUp(int $oldElo, int $newElo, EloRankType $rankType): bool
    {
        $usersToUpdate = $this->queryUsersToUpdate($oldElo, $newElo, $rankType);
        $rankChange = $usersToUpdate->count();
        $usersToUpdate->increment($rankType->databaseRankField());

        $this->{$rankType->databaseRankField()} = $this->{$rankType->databaseRankField()} - $rankChange;

        return $this->save();
    }

    private function rankDown(int $oldElo, int $newElo, EloRankType $rankType): bool
    {
        $usersToUpdate = $this->queryUsersToUpdate($oldElo, $newElo, $rankType);
        $rankChange = $usersToUpdate->count();
        $usersToUpdate->decrement($rankType->databaseRankField());

        $this->{$rankType->databaseRankField()} = $this->{$rankType->databaseRankField()} + $rankChange;

        return $this->save();
    }

    private function queryUsersToUpdate(int $oldElo, int $newElo, EloRankType $rankType): \Illuminate\Database\Eloquent\Builder
    {
        $query = self::where('id', '!=', $this->id)
            ->ranked($rankType);

        if ($this->{$rankType->databaseRankField()} === null) {
            return $query->where($rankType->databaseEloField(), '<=', $newElo);
        }

        $eloRange = [min($oldElo, $newElo), max($oldElo, $newElo)];
        if ($oldElo < $newElo) {
            return $query->where($rankType->databaseRankField(), '<', $this->{$rankType->databaseRankField()})
                ->whereBetween($rankType->databaseEloField(), $eloRange);
        }

        if ($oldElo > $newElo) {
            return $query->where($rankType->databaseRankField(), '>', $this->{$rankType->databaseRankField()})
                ->whereBetween($rankType->databaseEloField(), $eloRange);
        }

        return self::query();
    }
}
