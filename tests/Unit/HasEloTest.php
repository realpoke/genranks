<?php

namespace Tests\Unit;

use App\Enums\EloRankType;
use App\Enums\GameType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HasEloTest extends TestCase
{
    use RefreshDatabase;

    public function test_give_elo_increases_user_elo()
    {
        $user = User::factory()->create(['elo' => 1000, 'rank' => 1]);
        $result = $user->giveElo(50, EloRankType::ALL, GameType::ONE_ON_ONE);

        $this->assertTrue($result);
        $this->assertEquals(1050, $user->fresh()->elo);
    }

    public function test_take_elo_decreases_user_elo()
    {
        $user = User::factory()->create(['elo' => 1000, 'rank' => 1]);
        $result = $user->takeElo(50, EloRankType::ALL, GameType::ONE_ON_ONE);

        $this->assertTrue($result);
        $this->assertEquals(950, $user->fresh()->elo);
    }

    public function test_elo_cannot_go_below_one()
    {
        $user = User::factory()->create(['elo' => 10, 'rank' => 1]);
        $result = $user->takeElo(20, EloRankType::ALL, GameType::ONE_ON_ONE);

        $this->assertTrue($result);
        $this->assertEquals(1, $user->fresh()->elo);
    }

    public function test_rank_changes_when_elo_is_taken()
    {
        $user1 = User::factory()->create(['elo' => 1100, 'rank' => 1]);
        $user2 = User::factory()->create(['elo' => 1000, 'rank' => 2]);
        $user3 = User::factory()->create(['elo' => 900, 'rank' => 3]);

        $user1->takeElo(500, EloRankType::ALL, GameType::ONE_ON_ONE);

        $this->assertEquals(3, $user1->fresh()->rank);
        $this->assertEquals(1, $user2->fresh()->rank);
        $this->assertEquals(2, $user3->fresh()->rank);
    }

    public function test_rank_changes_when_elo_is_given()
    {
        $user1 = User::factory()->create(['elo' => 1100, 'rank' => 1]);
        $user2 = User::factory()->create(['elo' => 1000, 'rank' => 2]);
        $user3 = User::factory()->create(['elo' => 900, 'rank' => 3]);

        $user3->giveElo(500, EloRankType::ALL, GameType::ONE_ON_ONE);

        $this->assertEquals(2, $user1->fresh()->rank);
        $this->assertEquals(3, $user2->fresh()->rank);
        $this->assertEquals(1, $user3->fresh()->rank);
    }

    public function test_initial_rank_is_set_when_elo_changes()
    {
        $user = User::factory()->create(['elo' => 1000, 'rank' => null]);

        $user->giveElo(50, EloRankType::ALL, GameType::ONE_ON_ONE);

        $this->assertEquals(1, $user->fresh()->rank);
    }

    public function test_initial_rank_moves_other_users_down_when_elo_changes()
    {
        $user1 = User::factory()->create(['elo' => 1020, 'rank' => 1]);
        $user2 = User::factory()->create(['elo' => 900, 'rank' => 2]);
        $user3 = User::factory()->create(['elo' => 1000, 'rank' => null]);

        $user3->giveElo(50, EloRankType::ALL, GameType::ONE_ON_ONE);

        $this->assertEquals(1, $user3->fresh()->rank);
        $this->assertEquals(2, $user1->fresh()->rank);
        $this->assertEquals(3, $user2->fresh()->rank);
    }

    public function test_initial_rank_puts_user_at_the_bottom_when_elo_changes()
    {
        $user1 = User::factory()->create(['elo' => 1200, 'rank' => 1]);
        $user2 = User::factory()->create(['elo' => 950, 'rank' => 2]);
        $user3 = User::factory()->create(['elo' => 1000, 'rank' => null]);

        $user3->takeElo(200, EloRankType::ALL, GameType::ONE_ON_ONE);

        $this->assertEquals(1, $user1->fresh()->rank);
        $this->assertEquals(2, $user2->fresh()->rank);
        $this->assertEquals(3, $user3->fresh()->rank);
    }
}
