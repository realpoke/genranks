<?php

use App\Console\Commands\ValidateGames;
use Illuminate\Support\Facades\Schedule;

Schedule::command(ValidateGames::class)->everyFiveMinutes()->withoutOverlapping();
