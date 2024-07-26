<?php

namespace App\Traits;

use App\Enums\EloRankType;
use App\Enums\GameType;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HasElo
{
    public const DEFAULT_ELO = 1500;

    public function resetRank(string|array $rankField, string|array $eloField): bool
    {
        if (! is_array($rankField)) {
            $rankField = [$rankField];
        }

        if (! is_array($eloField)) {
            $eloField = [$eloField];
        }

        $updates = [];
        foreach ($rankField as $field) {
            $updates[$field] = null;
        }
        foreach ($eloField as $field) {
            $updates[$field] = self::DEFAULT_ELO;
        }

        return $this->update($updates);
    }

    public function giveElo(int $elo, EloRankType $rankType, GameType $gameType): bool
    {
        Log::info("Giving Elo to user {$this->id}: $elo, RankType: {$rankType->value}, GameType: {$gameType->value}");

        return $this->changeElo(
            abs($elo),
            $rankType->databaseRankField($gameType),
            $rankType->databaseEloField($gameType)
        );
    }

    public function takeElo(int $elo, EloRankType $rankType, GameType $gameType): bool
    {
        Log::info("Taking Elo from user {$this->id}: $elo, RankType: {$rankType->value}, GameType: {$gameType->value}");

        return $this->changeElo(
            -abs($elo),
            $rankType->databaseRankField($gameType),
            $rankType->databaseEloField($gameType)
        );
    }

    private function changeElo(int $eloChange, string $rankField, string $eloField): bool
    {
        Log::info("Changing Elo for user {$this->id}: EloChange: $eloChange, RankField: $rankField, EloField: $eloField");

        return DB::transaction(function () use ($eloChange, $rankField, $eloField) {
            // Validate Elo field
            if (! in_array($eloField, $this->getFillable(), true)) {
                Log::error("Elo field $eloField does not exist for user: {$this->id}");
                throw new \Exception("Invalid Elo field: $eloField");
            }

            // Validate rank field
            if (! in_array($rankField, $this->getFillable(), true)) {
                Log::error("Rank field $rankField does not exist for user: {$this->id}");
                throw new \Exception("Invalid rank field: $rankField");
            }

            // Lock the current user row
            $this->lockForUpdate();
            Log::info("Locked user {$this->id} for update");

            // Update Elo
            $oldElo = $this->$eloField;
            $newElo = max(1, $this->$eloField + $eloChange);
            Log::info("User {$this->id}: Old Elo: $oldElo, New Elo: $newElo");

            $this->$eloField = $newElo;
            if (! $this->save()) {
                Log::error("Failed to save user: {$this->id}");
                throw new \Exception("Failed to save user: {$this->id}");
            }
            Log::info("Saved new Elo for user {$this->id}");

            // Adjust ranks based on up-to-date data
            if (! $this->adjustRanks($oldElo, $newElo, $rankField, $eloField)) {
                Log::error("Failed to adjust ranks for user: {$this->id}");
                throw new \Exception('Failed to adjust ranks');
            }

            Log::info("Successfully changed Elo for user {$this->id}");

            return true; // Indicate success
        });
    }

    private function adjustRanks(int $oldElo, int $newElo, string $rankField, string $eloField): bool
    {
        Log::info("Adjusting ranks for user {$this->id}: Old Elo: $oldElo, New Elo: $newElo, RankField: $rankField");
        if ($this->$rankField === null) {
            Log::info("No rank found for user {$this->id}, setting initial rank");

            return $this->setInitialRank($newElo, $rankField, $eloField);
        }

        if ($oldElo < $newElo) {
            Log::info("Ranking up user {$this->id}");

            return $this->rankUp($oldElo, $newElo, $rankField, $eloField);
        }

        if ($oldElo > $newElo) {
            Log::info("Ranking down user {$this->id}");

            return $this->rankDown($oldElo, $newElo, $rankField, $eloField);
        }

        Log::info("No rank change for user {$this->id}");

        return true;
    }

    private function setInitialRank(int $newElo, string $rankField, string $eloField): bool
    {
        Log::info("Setting initial rank for user {$this->id}: New Elo: $newElo, RankField: $rankField");
        $usersToUpdate = $this->queryUsersToUpdate(0, $newElo, $rankField, $eloField);

        $bestRank = $usersToUpdate->count() > 0 ? $usersToUpdate->min($rankField) : null;
        Log::info("Best rank found for user {$this->id}: ".($bestRank ?? 'null'));

        if ($bestRank !== null) {
            $this->$rankField = $bestRank;
            $affected = $usersToUpdate->increment($rankField);
            Log::info("Incremented rank for $affected users");
        } else {
            $maxRank = User::where('id', '!=', $this->id)
                ->ranked($rankField)
                ->max($rankField);
            $this->$rankField = $maxRank === null ? 1 : ($maxRank + 1);
            Log::info('Set rank to '.$this->$rankField.' based on max existing rank');
        }

        if (! $this->save()) {
            Log::error("Failed to save initial rank for user: {$this->id}");

            return false;
        }
        Log::info("Saved initial rank for user {$this->id}: {$this->$rankField}");

        return true;
    }

    private function rankUp(int $oldElo, int $newElo, string $rankField, string $eloField): bool
    {
        Log::info("Ranking up user {$this->id}: Old Elo: $oldElo, New Elo: $newElo, RankField: $rankField");
        $usersToUpdate = $this->queryUsersToUpdate($oldElo, $newElo, $rankField, $eloField);

        $usersToUpdateCount = $usersToUpdate->count();
        Log::info('Ranks to decrement: '.$usersToUpdateCount);
        if (! is_null($usersToUpdateCount)) {
            $this->decrement($rankField, $usersToUpdateCount);
        }

        if (! $this->save()) {
            Log::error("Failed to save rank up for user: {$this->id}");

            return false;
        }

        $affected = $usersToUpdate->increment($rankField);
        Log::info("Incremented rank for $affected users");

        return true;
    }

    private function rankDown(int $oldElo, int $newElo, string $rankField, string $eloField): bool
    {
        Log::info("Ranking down user {$this->id}: Old Elo: $oldElo, New Elo: $newElo, RankField: $rankField");
        $usersToUpdate = $this->queryUsersToUpdate($oldElo, $newElo, $rankField, $eloField);

        $usersToUpdateCount = $usersToUpdate->count();
        Log::info('Amount to increment: '.$usersToUpdateCount);
        if (! is_null($usersToUpdateCount)) {
            $this->increment($rankField, $usersToUpdateCount);
        }

        if (! $this->save()) {
            Log::error("Failed to save rank down for user: {$this->id}");

            return false;
        }

        $affected = $usersToUpdate->decrement($rankField);
        Log::info("Decremented rank for $affected users");

        return true;
    }

    private function queryUsersToUpdate(int $oldElo, int $newElo, string $rankField, string $eloField): Builder
    {
        Log::debug("QueryUsersToUpdate params: oldElo = $oldElo, newElo = $newElo, rankField = $rankField, currentRank = {$this->$rankField}");

        $query = User::where('id', '!=', $this->id)
            ->ranked($rankField)
            ->whereBetween($eloField, [min($oldElo, $newElo), max($oldElo, $newElo)]);

        if (! empty($this->$rankField)) {
            $query->where(function ($q) use ($rankField, $oldElo, $newElo) {
                if ($newElo > $oldElo) {
                    $q->where($rankField, '<=', $this->$rankField);
                } else {
                    $q->where($rankField, '>=', $this->$rankField);
                }
            });
        }

        $sql = $query->toSql();
        $bindings = $query->getBindings();
        Log::debug("QueryUsersToUpdate SQL: $sql");
        Log::debug('QueryUsersToUpdate Bindings: '.json_encode($bindings));

        return $query->lockForUpdate();
    }
}
