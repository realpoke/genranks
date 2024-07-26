<?php

namespace Tests\Feature;

use App\Actions\GiveUserElo;
use App\Enums\EloRankType;
use App\Enums\GameType;
use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaderboardSimulationTest extends TestCase
{
    use RefreshDatabase;

    private GiveUserElo $giveUserElo;

    private Collection $users;

    protected const USER_AMOUNT = 200;

    protected const MAX_ITERATIONS = 1_000;

    protected function setUp(): void
    {
        parent::setUp();
        $this->giveUserElo = new GiveUserElo();
        $this->users = User::factory(self::USER_AMOUNT)->create();
    }

    public function test_leaderboard_simulation()
    {
        $maxIterations = self::MAX_ITERATIONS;
        $inconsistencyFound = false;

        for ($i = 0; $i < $maxIterations; $i++) {
            $simGame = $this->simulateGame();
            $rankDatabaseField = $simGame['rank'];
            $eloDatabaseField = $simGame['elo'];

            if (! $this->verifyLeaderboard($rankDatabaseField, $eloDatabaseField)) {
                $inconsistencyFound = true;
                break;
            }
        }

        if ($inconsistencyFound) {
            $rankedUsers = User::ranked($rankDatabaseField)->orderBy($rankDatabaseField)->get();
            echo 'All users ranks: '.implode(', ', $rankedUsers->pluck($rankDatabaseField)->toArray()).' and elos: '.implode(', ', $rankedUsers->pluck($eloDatabaseField)->toArray());
            echo PHP_EOL;
            echo "Leaderboard inconsistency found after {$i} iterations.";
            $this->fail("Leaderboard inconsistency found after {$i} iterations.");
        } else {
            echo "No inconsistencies found after {$maxIterations} iterations.";
            $this->assertTrue(true, "No inconsistencies found after {$maxIterations} iterations.");
        }
    }

    private function simulateGame()
    {
        $gameType = $this->randomGameType();
        $rankDatabaseField = EloRankType::ALL->databaseRankField($gameType);
        $eloDatabaseField = EloRankType::ALL->databaseEloField($gameType);
        $players = $this->selectRandomPlayers($gameType);
        $game = Game::factory()->create(['type' => $gameType]);

        $isFailedGame = rand(1, 10) === 1; // 10% chance for a failed game
        if (! $game->type->isFreeForAll()) {
            $winningTeam = rand(0, 1);
            foreach ($players as $index => $player) {
                $isWinner = ($index % 2 == $winningTeam);

                if ($isFailedGame && rand(0, 1)) {
                    $eloChange = null; // Some players in failed games have null elo_change
                } else {
                    $eloChange = $isWinner ? rand(1, 50) : -rand(1, 50);
                }

                $game->users()->attach($player->id, [
                    'elo_change' => $eloChange,
                    'summary' => ['Win' => $isWinner],
                    'header' => ['ArrayReplayOwnerSlot' => $index + 1],
                ]);
            }
        } else {
            foreach ($players as $index => $player) {
                if ($isFailedGame && rand(0, 1)) {
                    $eloChange = null; // Some players in failed games have null elo_change
                } else {
                    $eloChange = rand(0, 1) ? rand(1, 50) : -rand(1, 50);
                }
                $game->users()->attach($player->id, [
                    'elo_change' => $eloChange,
                    'summary' => ['Win' => $players->last()->id === $player->id],
                    'ffa_elimination_order' => $index + 1,
                    'header' => ['ArrayReplayOwnerSlot' => $index + 1],
                ]);
            }
        }

        $this->giveUserElo->__invoke($game);
        $game->refresh();
        $players = $game->users;

        echo "Game simulated: {$game->id} with {$players->count()} players by the type {$gameType->value}.";
        echo ' Failed game: '.($isFailedGame ? 'Yes' : 'No');
        echo ' Users ranks: '.implode(', ', $players->pluck($rankDatabaseField)->toArray()).' and elos: '.implode(', ', $players->pluck($eloDatabaseField)->toArray());
        echo ' Elo changes: '.implode(', ', $game->users->pluck('pivot.elo_change')->map(function ($change) {
            return $change === null ? 'null' : $change;
        })->toArray());

        if (! $isFailedGame && $players->count() != $players->unique($rankDatabaseField)->count()) {
            echo ' Players not unique, in a not-failed game!';
            $this->fail('Players not unique!');
        }

        echo PHP_EOL;
        ob_flush();
        flush();

        return ['rank' => $rankDatabaseField, 'elo' => $eloDatabaseField];
    }

    private function randomGameType()
    {
        return match (rand(0, 9)) {
            0 => GameType::ONE_ON_ONE,
            1 => GameType::TWO_ON_TWO,
            2 => GameType::THREE_ON_THREE,
            3 => GameType::FOUR_ON_FOUR,
            4 => GameType::FREE_FOR_ALL_THREE,
            5 => GameType::FREE_FOR_ALL_FOUR,
            6 => GameType::FREE_FOR_ALL_FIVE,
            7 => GameType::FREE_FOR_ALL_SIX,
            8 => GameType::FREE_FOR_ALL_SEVEN,
            9 => GameType::FREE_FOR_ALL_EIGHT,
        };
    }

    private function selectRandomPlayers($gameType)
    {
        $playerCount = match ($gameType) {
            GameType::ONE_ON_ONE => 2,
            GameType::TWO_ON_TWO => 4,
            GameType::THREE_ON_THREE => 6,
            GameType::FOUR_ON_FOUR => 8,
            GameType::FREE_FOR_ALL_THREE => 3,
            GameType::FREE_FOR_ALL_FOUR => 4,
            GameType::FREE_FOR_ALL_FIVE => 5,
            GameType::FREE_FOR_ALL_SIX => 6,
            GameType::FREE_FOR_ALL_SEVEN => 7,
            GameType::FREE_FOR_ALL_EIGHT => 8,
        };

        return $this->users->random($playerCount);
    }

    private function verifyLeaderboard(string $rankDatabaseField, string $eloDatabaseField)
    {
        $rankedUsers = User::ranked($rankDatabaseField)->orderBy($rankDatabaseField)->get();
        echo 'Verify, user count: '.$rankedUsers->count().' with rank field: '.$rankDatabaseField.' and elo field: '.$eloDatabaseField.PHP_EOL;

        $uniqueRanks = $rankedUsers->unique($rankDatabaseField)->count();
        if ($uniqueRanks !== $rankedUsers->count()) {
            echo 'Ranks not unique!'.PHP_EOL;

            return false;
        }

        // Check for gaps in ranks
        $expectedRank = 1;
        foreach ($rankedUsers as $user) {
            if ($user->{$rankDatabaseField} !== $expectedRank) {
                echo 'Rank gap found!'.PHP_EOL;

                return false; // Gap found
            }
            $expectedRank++;
        }

        // Check for correct ordering by ELO
        $previousElo = PHP_INT_MAX;
        foreach ($rankedUsers as $user) {
            if ($user->{$eloDatabaseField} > $previousElo) {
                echo 'Elo ordering incorrect!'.PHP_EOL;

                return false; // Incorrect ELO ordering
            }
            $previousElo = $user->{$eloDatabaseField};
        }

        return true;
    }
}
