<?php

namespace Tests\Unit;

use App\Actions\GiveUserElo;
use App\Enums\GameType;
use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GiveUserEloTest extends TestCase
{
    use RefreshDatabase;

    public function test_give_user_elo_updates_elo_for_all_users_in_game()
    {
        $user1 = User::factory()->create(['elo' => 1000, 'rank' => 1]);
        $user2 = User::factory()->create(['elo' => 1000, 'rank' => 2]);

        $game = Game::factory()->create(['type' => GameType::ONE_ON_ONE]);
        $game->users()->attach([
            $user1->id => ['elo_change' => 25, 'summary' => ['Win' => true], 'header' => ['ArrayReplayOwnerSlot' => 1]],
            $user2->id => ['elo_change' => -25, 'summary' => ['Win' => false], 'header' => ['ArrayReplayOwnerSlot' => 2]],
        ]);

        $action = new GiveUserElo();
        $result = $action($game);

        $this->assertTrue($result);
        $this->assertEquals(1025, $user1->fresh()->elo);
        $this->assertEquals(975, $user2->fresh()->elo);
    }

    public function test_give_user_elo_handles_invalid_elo_change()
    {
        $user = User::factory()->create(['elo' => 1000, 'rank' => 1]);
        $game = Game::factory()->create(['type' => GameType::ONE_ON_ONE]);
        $game->users()->attach([
            $user->id => ['elo_change' => null, 'summary' => ['Win' => true], 'header' => ['ArrayReplayOwnerSlot' => 1]],
        ]);

        $action = new GiveUserElo();
        $result = $action($game);

        $this->assertFalse($result);
        $this->assertEquals(1000, $user->fresh()->elo);
    }

    public function test_team_game_calculates_correct_elo()
    {
        $user1 = User::factory()->create(['elo' => 1250, 'rank' => 1]);
        $user2 = User::factory()->create(['elo' => 1000, 'rank' => 2]);
        $user3 = User::factory()->create(['elo' => 1000, 'rank' => null]);
        $user4 = User::factory()->create(['elo' => 860, 'rank' => 3]);

        $game = Game::factory()->create(['type' => GameType::TWO_ON_TWO]);
        $game->users()->attach([
            $user1->id => ['elo_change' => -25, 'summary' => ['Win' => false], 'header' => ['ArrayReplayOwnerSlot' => 1]],
            $user2->id => ['elo_change' => -25, 'summary' => ['Win' => false], 'header' => ['ArrayReplayOwnerSlot' => 2]],
            $user3->id => ['elo_change' => 25, 'summary' => ['Win' => true], 'header' => ['ArrayReplayOwnerSlot' => 3]],
            $user4->id => ['elo_change' => 25, 'summary' => ['Win' => true], 'header' => ['ArrayReplayOwnerSlot' => 4]],
        ]);

        $action = new GiveUserElo();
        $result = $action($game);

        $this->assertTrue($result);
        $this->assertEquals(1225, $user1->fresh()->elo);
        $this->assertEquals(975, $user2->fresh()->elo);
        $this->assertEquals(1025, $user3->fresh()->elo);
        $this->assertEquals(885, $user4->fresh()->elo);
    }

    public function test_on_fail_database_rollback_is_called()
    {
        $user1 = User::factory()->create(['elo' => 1000, 'rank' => 1]);
        $user2 = User::factory()->create(['elo' => 1000, 'rank' => 2]);
        $user3 = User::factory()->create(['elo' => 999, 'rank' => 3]);

        $game = Game::factory()->create(['type' => GameType::ONE_ON_ONE]);
        $game->users()->attach([
            $user1->id => ['elo_change' => 25, 'summary' => ['Win' => true], 'header' => ['ArrayReplayOwnerSlot' => 1]],
            $user2->id => ['elo_change' => -25, 'summary' => ['Win' => false], 'header' => ['ArrayReplayOwnerSlot' => 2]],
        ]);

        // Mock the DB facade to throw an exception during transaction
        \DB::shouldReceive('transaction')
            ->once()
            ->andThrow(new \Exception('Forced transaction failure'));

        $action = new GiveUserElo();
        $result = $action($game);

        $this->assertFalse($result);
        $this->assertEquals(1000, $user1->fresh()->elo);
        $this->assertEquals(1000, $user2->fresh()->elo);
        $this->assertEquals(999, $user3->fresh()->elo);
        $this->assertEquals(1, $user1->fresh()->rank);
        $this->assertEquals(2, $user2->fresh()->rank);
        $this->assertEquals(3, $user3->fresh()->rank);
    }

    public function test_on_fail_elo_change_is_reset_to_zero()
    {
        $user1 = User::factory()->create(['elo' => 1000, 'rank' => 1]);
        $user2 = User::factory()->create(['elo' => 1000, 'rank' => 2]);

        $game = Game::factory()->create(['type' => GameType::ONE_ON_ONE]);
        $game->users()->attach([
            $user1->id => ['elo_change' => 25, 'summary' => ['Win' => true], 'header' => ['ArrayReplayOwnerSlot' => 1]],
            $user2->id => ['elo_change' => -25, 'summary' => ['Win' => false], 'header' => ['ArrayReplayOwnerSlot' => 2]],
        ]);

        // Mock the DB facade to throw an exception during transaction
        \DB::shouldReceive('transaction')
            ->once()
            ->andThrow(new \Exception('Forced transaction failure'));

        $action = new GiveUserElo();
        $result = $action($game);
        $game->refresh();

        $this->assertFalse($result);
        $game->users()->get()->each(function ($user) {
            $this->assertEquals(0, $user->pivot->elo_change);
        });
    }
}
