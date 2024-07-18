<?php

namespace App\Traits;

use App\Enums\EloRankType;
use App\Enums\GameType;
use App\Models\Game;
use Illuminate\Contracts\Database\Eloquent\Builder;

trait HasElo
{
    public function newElo(
        int $newElo,
        ?Game $game = null,
        EloRankType $rankType = EloRankType::ALL,
        GameType $gameType = GameType::ONE_ON_ONE
    ): bool {
        if ($newElo == $this->{$rankType->databaseEloField($gameType)}) {
            return true;
        }

        if ($newElo > $this->{$rankType->databaseEloField($gameType)}) {
            return $this->giveElo($newElo - $this->{$rankType->databaseEloField($gameType)}, $game, $rankType, $gameType);
        }

        if ($newElo < $this->{$rankType->databaseEloField($gameType)}) {
            return $this->takeElo($this->{$rankType->databaseEloField($gameType)} - $newElo, $game, $rankType, $gameType);
        }

        return false;
    }

    public function giveElo(int $elo, ?Game $game, EloRankType $rankType, GameType $gameType): bool
    {
        return $this->changeElo(abs($elo), $game, $rankType, $gameType);
    }

    public function takeElo(int $elo, ?Game $game, EloRankType $rankType, GameType $gameType): bool
    {
        return $this->changeElo(-abs($elo), $game, $rankType, $gameType);
    }

    public function changeElo(int $changeElo, ?Game $game, EloRankType $rankType, GameType $gameType): bool
    {
        if (! is_null($game)) {
            $this->games()->updateExistingPivot($game->id, ['elo_change' => $changeElo]);
        }
        $oldElo = $this->{$rankType->databaseEloField($gameType)};
        $this->{$rankType->databaseEloField($gameType)} = max(0, $oldElo + $changeElo);

        // TODO: Use database transaction to make sure rank and elo is in sync
        return $this->save() && $this->adjustRanks($oldElo, $this->{$rankType->databaseEloField($gameType)}, $rankType, $gameType);
    }

    private function adjustRanks(int $oldElo, int $newElo, EloRankType $rankType, GameType $gameType): bool
    {
        // TODO: Adjusting ranks are messing up the rankings many users have the same rank and there are gaps in the ranks
        if ($this->{$rankType->databaseRankField($gameType)} === null) {
            return $this->setInitialRank($newElo, $rankType, $gameType);
        }

        if ($oldElo < $newElo) {
            return $this->rankUp($oldElo, $newElo, $rankType, $gameType);
        }

        if ($oldElo > $newElo) {
            return $this->rankDown($oldElo, $newElo, $rankType, $gameType);
        }

        return false;
    }

    private function setInitialRank(int $elo, EloRankType $rankType, GameType $gameType): bool
    {
        $usersToUpdate = $this->queryUsersToUpdate(0, $elo, $rankType, $gameType);
        $bestRank = $usersToUpdate->min($rankType->databaseRankField($gameType));

        if ($bestRank !== null) {
            $this->rank = $bestRank;
            $usersToUpdate->increment($rankType->databaseRankField($gameType));
        } else {
            $maxRank = self::where('id', '!=', $this->id)
                ->ranked($rankType)
                ->max($rankType->databaseRankField($gameType));
            $this->{$rankType->databaseRankField($gameType)} = $maxRank === null ? 1 : ($maxRank + 1);
        }

        return $this->save();
    }

    private function rankUp(int $oldElo, int $newElo, EloRankType $rankType, GameType $gameType): bool
    {
        $usersToUpdate = $this->queryUsersToUpdate($oldElo, $newElo, $rankType, $gameType);
        $rankChange = $usersToUpdate->count();
        $usersToUpdate->increment($rankType->databaseRankField($gameType));

        $this->{$rankType->databaseRankField($gameType)} = $this->{$rankType->databaseRankField($gameType)} - $rankChange;

        return $this->save();
    }

    private function rankDown(int $oldElo, int $newElo, EloRankType $rankType, GameType $gameType): bool
    {
        $usersToUpdate = $this->queryUsersToUpdate($oldElo, $newElo, $rankType, $gameType);
        $rankChange = $usersToUpdate->count();
        $usersToUpdate->decrement($rankType->databaseRankField($gameType));

        $this->{$rankType->databaseRankField($gameType)} = $this->{$rankType->databaseRankField($gameType)} + $rankChange;

        return $this->save();
    }

    private function queryUsersToUpdate(int $oldElo, int $newElo, EloRankType $rankType, GameType $gameType): Builder
    {
        $query = self::where('id', '!=', $this->id)
            ->ranked($rankType);

        if ($this->{$rankType->databaseRankField($gameType)} === null) {
            return $query->where($rankType->databaseEloField($gameType), '<=', $newElo);
        }

        $eloRange = [min($oldElo, $newElo), max($oldElo, $newElo)];
        if ($oldElo < $newElo) {
            return $query->where($rankType->databaseRankField($gameType), '<', $this->{$rankType->databaseRankField($gameType)})
                ->whereBetween($rankType->databaseEloField($gameType), $eloRange);
        }

        if ($oldElo > $newElo) {
            return $query->where($rankType->databaseRankField($gameType), '>', $this->{$rankType->databaseRankField($gameType)})
                ->whereBetween($rankType->databaseEloField($gameType), $eloRange);
        }

        return self::query();
    }
}
