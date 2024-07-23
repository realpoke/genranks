<?php

namespace App\Console\Commands;

use App\Enums\EloRankType;
use App\Enums\GameType;
use App\Models\User;
use Illuminate\Console\Command;

class ResetLadder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ranks:reset
                            {--monthly : Reset monthly rankings}
                            {--weekly : Reset weekly rankings}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resets the weekly and/or monthly ladder rankings.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $resetMonthly = $this->option('monthly');
        $resetWeekly = $this->option('weekly');

        if (! $resetMonthly && ! $resetWeekly) {
            $resetMonthly = $resetWeekly = true;
        }

        $this->info('Resetting ladder rankings...');

        $databaseRankFields = [];
        $databaseEloFields = [];

        if ($resetMonthly) {
            $databaseRankFields[] = EloRankType::MONTHLY->databaseRankField(GameType::ONE_ON_ONE);
            $databaseRankFields[] = EloRankType::MONTHLY->databaseRankField(GameType::FREE_FOR_ALL_EIGHT);
            $databaseEloFields[] = EloRankType::MONTHLY->databaseEloField(GameType::ONE_ON_ONE);
            $databaseEloFields[] = EloRankType::MONTHLY->databaseEloField(GameType::FREE_FOR_ALL_EIGHT);
        }

        if ($resetWeekly) {
            $databaseRankFields[] = EloRankType::WEEKLY->databaseRankField(GameType::ONE_ON_ONE);
            $databaseRankFields[] = EloRankType::WEEKLY->databaseRankField(GameType::FREE_FOR_ALL_EIGHT);
            $databaseEloFields[] = EloRankType::WEEKLY->databaseEloField(GameType::ONE_ON_ONE);
            $databaseEloFields[] = EloRankType::WEEKLY->databaseEloField(GameType::FREE_FOR_ALL_EIGHT);
        }

        foreach ($databaseRankFields as $databaseRankField) {
            $this->info('Resetting ranks for '.$databaseRankField);
            User::ranked($databaseRankField)
                ->each(function ($user) use ($databaseRankFields, $databaseEloFields) {
                    $user->resetRank($databaseRankFields, $databaseEloFields);
                });
        }

        $this->info('Ranks and elo reset completed.');
    }
}
