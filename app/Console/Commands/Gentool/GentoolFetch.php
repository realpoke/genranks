<?php

namespace App\Console\Commands\Gentool;

use App\Console\Commands\ClearOldGames;
use App\Contracts\Gentool\CreatesGameContract;
use App\Contracts\Gentool\GetsUsersContract;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class GentoolFetch extends Command
{
    protected $signature = 'gentool:fetch
                            {--day=0 : How many days to go back in time and fetch that day.}';

    protected $description = 'Fetch GenTool replays and parse them into games and player stats.';

    protected GetsUsersContract $userGetter;

    protected CreatesGameContract $gameCreator;

    public function __construct(
        GetsUsersContract $userGetter,
        CreatesGameContract $gameCreator
    ) {
        $this->userGetter = $userGetter;
        $this->gameCreator = $gameCreator;

        parent::__construct();
    }

    // TODO: Set contracts in app service provider.
    public function handle()
    {
        Cache::put('gentool_fetch_command_running', true);
        Cache::put('gentool_fetch_command_progress', 0);
        $this->info('Fetching data from GenTool!');

        $users = $this->userGetter->users(
            Carbon::now()->addDays(-$this->option('day'))
        );
        $this->info('Fetching from '.$users->count().' users.');
        $progress = $this->output->createProgressBar($users->count());
        $progress->setFormat('%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s% %message%');
        $this->gameCreator->create($users, $progress);

        $progress->finish();
        Cache::forget('gentool_fetch_command_running');
        Cache::forget('gentool_fetch_command_progress');
        $this->info('Data fetching done!');

        Artisan::call(ClearOldGames::class);
    }
}
