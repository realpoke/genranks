<?php

use App\Console\Commands\GenTool\UploadRandomUser;
use App\Console\Commands\ValidateGames;
use Illuminate\Support\Facades\Schedule;

Schedule::command(ValidateGames::class)->everyFiveMinutes()->withoutOverlapping();
Schedule::command(UploadRandomUser::class)->everyFiveMinutes()->withoutOverlapping();
