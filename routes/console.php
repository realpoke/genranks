<?php

use App\Console\Commands\ClearGames;
use App\Console\Commands\ClearReplays;
use App\Console\Commands\GenTool\UploadRandomUser;
use Illuminate\Support\Facades\Schedule;

Schedule::command(UploadRandomUser::class)->everyTwoMinutes()->withoutOverlapping();
Schedule::command(ClearReplays::class)->everyThirtyMinutes()->withoutOverlapping();
Schedule::command(ClearGames::class)->everyThirtyMinutes()->withoutOverlapping();
