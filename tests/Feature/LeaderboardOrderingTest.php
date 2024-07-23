<?php

namespace Tests\Feature;

use App\Actions\GiveUserElo;
use App\Enums\GameType;
use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaderboardOrderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_leaderboard_ordering_after_multiple_games()
    {
        // Create users with initial ELOs and ranks
        $user1 = User::factory()->create(['elo' => 1100, 'rank' => 1]);
        $user2 = User::factory()->create(['elo' => 1000, 'rank' => 2]);
        $user3 = User::factory()->create(['elo' => 1000, 'rank' => 3]);
        $user4 = User::factory()->create(['elo' => 900, 'rank' => 4]);
        $user5 = User::factory()->create(['elo' => 1000, 'rank' => null]);

        $giveUserElo = new GiveUserElo();

        // Game 1: User1 wins, User2 loses
        $game1 = Game::factory()->create(['type' => GameType::ONE_ON_ONE]);
        $game1->users()->attach([
            $user1->id => ['elo_change' => 25, 'summary' => ['Win' => true], 'header' => ['ArrayReplayOwnerSlot' => 1]],
            $user2->id => ['elo_change' => -25, 'summary' => ['Win' => false], 'header' => ['ArrayReplayOwnerSlot' => 2]],
        ]);
        $giveUserElo($game1);
        $this->assertEloAndRank($user1, 1125, 1);
        $this->assertEloAndRank($user2, 975, 3);
        $this->assertEloAndRank($user3, 1000, 2);
        $this->assertEloAndRank($user4, 900, 4);
        $this->assertEloAndRank($user5, 1000, null);

        // Game 2: User3 wins, User1 loses
        $game2 = Game::factory()->create(['type' => GameType::ONE_ON_ONE]);
        $game2->users()->attach([
            $user3->id => ['elo_change' => 30, 'summary' => ['Win' => true], 'header' => ['ArrayReplayOwnerSlot' => 1]],
            $user1->id => ['elo_change' => -30, 'summary' => ['Win' => false], 'header' => ['ArrayReplayOwnerSlot' => 2]],
        ]);
        $giveUserElo($game2);
        $this->assertEloAndRank($user1, 1095, 1);
        $this->assertEloAndRank($user2, 975, 3);
        $this->assertEloAndRank($user3, 1030, 2);
        $this->assertEloAndRank($user4, 900, 4);
        $this->assertEloAndRank($user5, 1000, null);

        // Game 3: User2 wins, User3 loses
        $game3 = Game::factory()->create(['type' => GameType::ONE_ON_ONE]);
        $game3->users()->attach([
            $user2->id => ['elo_change' => 20, 'summary' => ['Win' => true], 'header' => ['ArrayReplayOwnerSlot' => 1]],
            $user3->id => ['elo_change' => -20, 'summary' => ['Win' => false], 'header' => ['ArrayReplayOwnerSlot' => 2]],
        ]);
        $giveUserElo($game3);

        $this->assertEloAndRank($user1, 1095, 1);
        $this->assertEloAndRank($user2, 995, 3);
        $this->assertEloAndRank($user3, 1010, 2);
        $this->assertEloAndRank($user4, 900, 4);
        $this->assertEloAndRank($user5, 1000, null);

        // Game 4: User4 wins, User5 loses
        $game4 = Game::factory()->create(['type' => GameType::ONE_ON_ONE]);
        $game4->users()->attach([
            $user4->id => ['elo_change' => 40, 'summary' => ['Win' => true], 'header' => ['ArrayReplayOwnerSlot' => 1]],
            $user5->id => ['elo_change' => -40, 'summary' => ['Win' => false], 'header' => ['ArrayReplayOwnerSlot' => 2]],
        ]);
        $giveUserElo($game4);
        $this->assertEloAndRank($user1, 1095, 1);
        $this->assertEloAndRank($user2, 995, 3);
        $this->assertEloAndRank($user3, 1010, 2);
        $this->assertEloAndRank($user4, 940, 5);
        $this->assertEloAndRank($user5, 960, 4);

        // Game 5: User1 and User2 win, User3 and User4 lose
        $game5 = Game::factory()->create(['type' => GameType::TWO_ON_TWO]);
        $game5->users()->attach([
            $user1->id => ['elo_change' => 15, 'summary' => ['Win' => true], 'header' => ['ArrayReplayOwnerSlot' => 1]],
            $user2->id => ['elo_change' => 15, 'summary' => ['Win' => true], 'header' => ['ArrayReplayOwnerSlot' => 2]],
            $user3->id => ['elo_change' => -15, 'summary' => ['Win' => false], 'header' => ['ArrayReplayOwnerSlot' => 3]],
            $user4->id => ['elo_change' => -15, 'summary' => ['Win' => false], 'header' => ['ArrayReplayOwnerSlot' => 4]],
        ]);
        $giveUserElo($game5);
        $this->assertEloAndRank($user1, 1110, 1);
        $this->assertEloAndRank($user2, 1010, 2);
        $this->assertEloAndRank($user3, 995, 3);
        $this->assertEloAndRank($user4, 925, 5);
        $this->assertEloAndRank($user5, 960, 4);

        // Game 6: User5 wins, User1 loses
        $game6 = Game::factory()->create(['type' => GameType::ONE_ON_ONE]);
        $game6->users()->attach([
            $user5->id => ['elo_change' => 35, 'summary' => ['Win' => true], 'header' => ['ArrayReplayOwnerSlot' => 1]],
            $user1->id => ['elo_change' => -35, 'summary' => ['Win' => false], 'header' => ['ArrayReplayOwnerSlot' => 2]],
        ]);
        $giveUserElo($game6);
        $this->assertEloAndRank($user1, 1075, 1);
        $this->assertEloAndRank($user2, 1010, 2);
        $this->assertEloAndRank($user3, 995, 4);
        $this->assertEloAndRank($user4, 925, 5);
        $this->assertEloAndRank($user5, 995, 3);

        // Game 7: User3 wins, User2 and User4 lose
        $game7 = Game::factory()->create(['type' => GameType::ONE_ON_ONE]);
        $game7->users()->attach([
            $user3->id => ['elo_change' => 40, 'summary' => ['Win' => true], 'header' => ['ArrayReplayOwnerSlot' => 1]],
            $user2->id => ['elo_change' => -20, 'summary' => ['Win' => false], 'header' => ['ArrayReplayOwnerSlot' => 2]],
            $user4->id => ['elo_change' => -20, 'summary' => ['Win' => false], 'header' => ['ArrayReplayOwnerSlot' => 3]],
        ]);
        $giveUserElo($game7);
        $this->assertEloAndRank($user1, 1075, 1);
        $this->assertEloAndRank($user2, 990, 4);
        $this->assertEloAndRank($user3, 1035, 2);
        $this->assertEloAndRank($user4, 905, 5);
        $this->assertEloAndRank($user5, 995, 3);

        // Game 8: User4 and User5 win, User1 and User2 lose
        $game8 = Game::factory()->create(['type' => GameType::TWO_ON_TWO]);
        $game8->users()->attach([
            $user4->id => ['elo_change' => 25, 'summary' => ['Win' => true], 'header' => ['ArrayReplayOwnerSlot' => 1]],
            $user5->id => ['elo_change' => 25, 'summary' => ['Win' => true], 'header' => ['ArrayReplayOwnerSlot' => 2]],
            $user1->id => ['elo_change' => -25, 'summary' => ['Win' => false], 'header' => ['ArrayReplayOwnerSlot' => 3]],
            $user2->id => ['elo_change' => -25, 'summary' => ['Win' => false], 'header' => ['ArrayReplayOwnerSlot' => 4]],
        ]);
        $giveUserElo($game8);
        $this->assertEloAndRank($user1, 1050, 1);
        $this->assertEloAndRank($user2, 965, 4);
        $this->assertEloAndRank($user3, 1035, 2);
        $this->assertEloAndRank($user4, 930, 5);
        $this->assertEloAndRank($user5, 1020, 3);

        // Game 9: User2 wins, User5 loses
        $game9 = Game::factory()->create(['type' => GameType::ONE_ON_ONE]);
        $game9->users()->attach([
            $user2->id => ['elo_change' => 30, 'summary' => ['Win' => true], 'header' => ['ArrayReplayOwnerSlot' => 1]],
            $user5->id => ['elo_change' => -30, 'summary' => ['Win' => false], 'header' => ['ArrayReplayOwnerSlot' => 2]],
        ]);
        $giveUserElo($game9);
        $this->assertEloAndRank($user1, 1050, 1);
        $this->assertEloAndRank($user2, 995, 3);
        $this->assertEloAndRank($user3, 1035, 2);
        $this->assertEloAndRank($user4, 930, 5);
        $this->assertEloAndRank($user5, 990, 4);

        // Game 10: User3 wins, User1, User4, and User5 lose
        $game10 = Game::factory()->create(['type' => GameType::ONE_ON_ONE]);
        $game10->users()->attach([
            $user3->id => ['elo_change' => 45, 'summary' => ['Win' => true], 'header' => ['ArrayReplayOwnerSlot' => 1]],
            $user1->id => ['elo_change' => -15, 'summary' => ['Win' => false], 'header' => ['ArrayReplayOwnerSlot' => 2]],
            $user4->id => ['elo_change' => -15, 'summary' => ['Win' => false], 'header' => ['ArrayReplayOwnerSlot' => 3]],
            $user5->id => ['elo_change' => -15, 'summary' => ['Win' => false], 'header' => ['ArrayReplayOwnerSlot' => 4]],
        ]);
        $giveUserElo($game10);
        $this->assertEloAndRank($user1, 1035, 2);
        $this->assertEloAndRank($user2, 995, 3);
        $this->assertEloAndRank($user3, 1080, 1);
        $this->assertEloAndRank($user4, 915, 5);
        $this->assertEloAndRank($user5, 975, 4);
    }

    private function assertEloAndRank($user, $expectedElo, $expectedRank)
    {
        $user->refresh();
        $this->assertEquals($expectedElo, $user->elo, "Expected ELO {$expectedElo} for user {$user->id}, but got {$user->elo}");
        $this->assertEquals($expectedRank, $user->rank, "Expected rank {$expectedRank} for user {$user->id}, but got {$user->rank}");
    }
}
