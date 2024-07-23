<?php

use App\Console\Commands\ClearGames;
use App\Console\Commands\ClearReplays;
use App\Console\Commands\GenTool\UploadLatest;
use App\Console\Commands\ResetLadder;
use Illuminate\Support\Facades\Schedule;

Schedule::command(UploadLatest::class)->everyTenMinutes();
Schedule::command(ClearReplays::class)->everyThirtyMinutes()->withoutOverlapping();
Schedule::command(ClearGames::class, ['--cleanusers'])->everyThirtyMinutes()->withoutOverlapping();

Schedule::command(ResetLadder::class, ['--weekly'])->weeklyOn(2, '02:00');
Schedule::command(ResetLadder::class, ['--monthly'])
    ->monthly()
    ->when(function () {
        return date('N') == 2 && date('j') <= 7;
    })
    ->at('02:00');
