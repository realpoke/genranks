<?php

use App\Console\Commands\ClearGames;
use App\Console\Commands\ClearReplays;
use App\Console\Commands\GenTool\UploadLatest;
use Illuminate\Support\Facades\Schedule;

Schedule::command(UploadLatest::class)->everyTenMinutes();
Schedule::command(ClearReplays::class)->everyThirtyMinutes()->withoutOverlapping();
Schedule::command(ClearGames::class, ['--cleanusers'])->everyThirtyMinutes()->withoutOverlapping();
