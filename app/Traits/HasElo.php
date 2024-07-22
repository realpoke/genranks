<?php

namespace App\Traits;

use App\Enums\EloRankType;
use App\Enums\GameType;
use App\Models\Game;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        $changeElo = $newElo - $this->{$rankType->databaseEloField($gameType)};

        return $this->changeElo($changeElo, $game, $rankType, $gameType);
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
        return DB::transaction(function () use ($changeElo, $game, $rankType, $gameType) {
            // Lock the current user's record
            $this->lockForUpdate();

            if (! is_null($game)) {
                $this->games()->updateExistingPivot($game->id, ['elo_change' => $changeElo]);
            }

            // Update Elo
            $oldElo = $this->{$rankType->databaseEloField($gameType)};
            $newElo = max(0, $oldElo + $changeElo);

            $this->{$rankType->databaseEloField($gameType)} = $newElo;
            $saveSuccess = $this->save();

            if (! $saveSuccess) {
                Log::error("Failed to save user {$this->id}");

                return false;
            }

            // Adjust ranks based on up-to-date data
            $adjustSuccess = $this->adjustRanks($oldElo, $newElo, $rankType, $gameType);

            return $adjustSuccess;
        });
    }

    private function adjustRanks(int $oldElo, int $newElo, EloRankType $rankType, GameType $gameType): bool
    {
        if ($this->{$rankType->databaseRankField($gameType)} === null) {
            return $this->setInitialRank($newElo, $rankType, $gameType);
        }

        if ($oldElo < $newElo) {
            return $this->rankUp($oldElo, $newElo, $rankType, $gameType);
        }

        if ($oldElo > $newElo) {
            return $this->rankDown($oldElo, $newElo, $rankType, $gameType);
        }

        return true;
    }

    private function setInitialRank(int $elo, EloRankType $rankType, GameType $gameType): bool
    {
        return DB::transaction(function () use ($elo, $rankType, $gameType) {
            $usersToUpdate = $this->queryUsersToUpdate(0, $elo, $rankType, $gameType);
            $usersToUpdate->lockForUpdate();

            $bestRank = $usersToUpdate->min($rankType->databaseRankField($gameType));

            if ($bestRank !== null) {
                $this->{$rankType->databaseRankField($gameType)} = $bestRank;
                $usersToUpdate->increment($rankType->databaseRankField($gameType));
            } else {
                $maxRank = self::where('id', '!=', $this->id)
                    ->ranked($rankType, $gameType)
                    ->max($rankType->databaseRankField($gameType));
                $this->{$rankType->databaseRankField($gameType)} = $maxRank === null ? 1 : ($maxRank + 1);
            }

            return $this->save();
        });
    }

    private function rankUp(int $oldElo, int $newElo, EloRankType $rankType, GameType $gameType): bool
    {
        return DB::transaction(function () use ($oldElo, $newElo, $rankType, $gameType) {
            $usersToUpdate = $this->queryUsersToUpdate($oldElo, $newElo, $rankType, $gameType);
            $usersToUpdate->lockForUpdate();

            $rankChange = $usersToUpdate->count();
            $usersToUpdate->increment($rankType->databaseRankField($gameType));

            $this->{$rankType->databaseRankField($gameType)} = $this->{$rankType->databaseRankField($gameType)} - $rankChange;

            return $this->save();
        });
    }

    private function rankDown(int $oldElo, int $newElo, EloRankType $rankType, GameType $gameType): bool
    {
        return DB::transaction(function () use ($oldElo, $newElo, $rankType, $gameType) {

            $usersToUpdate = $this->queryUsersToUpdate($oldElo, $newElo, $rankType, $gameType);
            $usersToUpdate->lockForUpdate();

            $rankChange = $usersToUpdate->count();
            $usersToUpdate->decrement($rankType->databaseRankField($gameType));

            $this->{$rankType->databaseRankField($gameType)} = $this->{$rankType->databaseRankField($gameType)} + $rankChange;

            return $this->save();
        });
    }

    private function queryUsersToUpdate(int $oldElo, int $newElo, EloRankType $rankType, GameType $gameType): Builder
    {
        $query = User::where('id', '!=', $this->id)->ranked($rankType, $gameType);

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

        return $query;
    }
}
