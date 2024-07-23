<?php

namespace App\Console\Commands;

use App\Enums\EloRankType;
use App\Enums\GameType;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CalibrateRanks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ranks:calibrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calibrates ranks for all users, based on their elo. Weely, monthly and all time.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Calibrating ranks...');

        $this->calibrateAllTime();
        $this->calibrateWeekly();
        $this->calibrateMonthly();

        $this->info('Done.');
    }

    private function calibrateAllTime(): void
    {
        $this->info('Calibrating all-time ranks...');
        $this->assignRanks(EloRankType::ALL, GameType::ONE_ON_ONE);
        $this->assignRanks(EloRankType::ALL, GameType::FREE_FOR_ALL_EIGHT);
    }

    private function calibrateWeekly(): void
    {
        $this->info('Calibrating weekly ranks...');
        $this->assignRanks(EloRankType::WEEKLY, GameType::ONE_ON_ONE);
        $this->assignRanks(EloRankType::WEEKLY, GameType::FREE_FOR_ALL_EIGHT);
    }

    private function calibrateMonthly(): void
    {
        $this->info('Calibrating monthly ranks...');
        $this->assignRanks(EloRankType::MONTHLY, GameType::ONE_ON_ONE);
        $this->assignRanks(EloRankType::MONTHLY, GameType::FREE_FOR_ALL_EIGHT);
    }

    private function assignRanks(EloRankType $timePeriod, GameType $gameType): void
    {
        $rankField = $timePeriod->databaseRankField($gameType);
        $eloField = $timePeriod->databaseEloField($gameType);

        $subQuery = User::query()
            ->ranked($rankField)
            ->select('id')
            ->selectRaw("@rank := @rank + 1 as new_rank, $eloField")
            ->orderByDesc($eloField)
            ->toSql();

        DB::statement('SET @rank := 0');

        $updateQuery = "UPDATE users u
                        JOIN ($subQuery) r ON u.id = r.id
                        SET u.$rankField = r.new_rank";

        DB::update($updateQuery, User::query()->getBindings());
    }
}
