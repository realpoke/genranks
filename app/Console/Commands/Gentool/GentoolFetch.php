<?php

namespace App\Console\Commands\Gentool;

use App\Contracts\Gentool\CreatesGameContract;
use App\Contracts\Gentool\GetsUsersContract;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class GentoolFetch extends Command
{
    protected $signature = 'gentool:fetch
                            {--day=0 : How many days to go back in time and fetch that day.}';

    protected $description = 'Fetch GenTool replays and parse them into games and player stats.';

    // TODO: Set contracts in app service provider.
    public function handle(
        GetsUsersContract $userGetter,
        CreatesGameContract $gameCreator,
    ) {
        Cache::put('gentool_fetch_command_running', true);
        $this->info('Fetching data from GenTool!');

        $users = $userGetter->users(
            Carbon::now()->addDays(-$this->option('day'))
        );
        $this->info('Fetching from '.$users->count().' users.');

        $gameCreator->create($users);

        Cache::forget('gentool_fetch_command_running');
        $this->info('Data fetching done!');
    }
}
