<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ClearUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes old fake users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fakeUsers = User::fake()->get();

        foreach ($fakeUsers as $user) {
            if ($user->games()->doesntExist()) {
                $user->delete();
            }
        }

        $this->info('Fake users cleanup completed.');
    }
}
