<?php

namespace App\Console\Commands\GenTool;

use App\Contracts\GenTool\CreatesGenToolUserContract;
use App\Contracts\GenTool\GetsGenToolUsersContract;
use App\Contracts\GenTool\GetsValidGenToolGamesContract;
use App\Contracts\GenTool\SearchesForGenToolUserContract;
use App\Jobs\ProcessReplay;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class UploadRandomUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gentool:uploadrandom {--loop : Loop infinitely}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch random GenTool user replays.';

    /**
     * Execute the console command.
     */
    public function handle(
        GetsGenToolUsersContract $userGetter,
        CreatesGenToolUserContract $userCreator,
        GetsValidGenToolGamesContract $gameGetter,
        SearchesForGenToolUserContract $userSearcher,
    ) {
        do {
            // Get random user from today.
            $users = $userGetter(Carbon::now());
            if ($users->isEmpty()) {
                $this->info('No users found.');

                continue;
            }

            $userValue = $users->random();
            $userKey = $users->search($userValue);
            $this->info('Processing user: '.$userKey);

            // Find valid replays and store opponents
            $games = $gameGetter($userValue);
            if ($games->isEmpty()) {
                $this->info('No valid games found.');

                continue;
            }

            // Create or find fake user
            $user = $userCreator($userKey)->first();
            $this->info('User created: '.$userKey);

            $opponents = collect();
            foreach ($games as $replayName => $replayURL) {
                $this->info($replayName);
                $segments = explode('_', $replayName);

                // Remove the first two segments (timestamp and game type)
                array_shift($segments);
                array_shift($segments);

                // Get the user's nickname
                $userNickname = explode('_', $userKey)[0];

                // Filter out the user's nickname and add remaining unique opponents
                $gameOpponents = array_filter($segments, function ($segment) use ($userNickname) {
                    return $segment !== $userNickname;
                });

                $opponents = $opponents->merge($gameOpponents)->unique();

                $fileContent = file_get_contents($replayURL);
                $fileName = $user->id.'_'.time().'_'.$opponents->count().'_replay';
                $uploadedReplay = Storage::disk('replays')->put($fileName, $fileContent);

                if ($uploadedReplay != false) {
                    $this->info('Uploaded replay: '.$fileName);
                    ProcessReplay::dispatch($user, $fileName, anticheat: true);
                }
            }

            // Get replays from opponents
            foreach ($userSearcher(Carbon::now(), ...$opponents->unique()->toArray()) as $userNickname => $userUrl) {
                $this->info($userNickname);
                // Find valid replays and store
                $opponentGames = $gameGetter($userUrl);
                if ($opponentGames->isEmpty()) {
                    $this->info('No opponent valid games found.');

                    continue 2;
                }
                $userOpponent = $userCreator($userNickname)->first();
                $this->info('Opponent created: '.$userNickname);

                $replayNumber = 0;
                foreach ($opponentGames as $OpponentReplayName => $opponentReplayURL) {
                    $replayNumber++;
                    $this->info($OpponentReplayName);

                    $fileContent = file_get_contents($opponentReplayURL);
                    $fileName = $userOpponent->id.'_'.time().'_'.$replayNumber.'_replay';
                    $uploadedReplay = Storage::disk('replays')->put($fileName, $fileContent);

                    if ($uploadedReplay != false) {
                        $this->info('Uploaded opponent replay: '.$fileName);
                        ProcessReplay::dispatch($userOpponent, $fileName, anticheat: true);
                    }
                }
            }
        } while ($this->option('loop'));
    }
}
