<?php

use App\Console\Commands\GenTool\UploadRandomUser;
use Illuminate\Support\Facades\Schedule;

Schedule::command(UploadRandomUser::class)->everyTwoMinutes()->withoutOverlapping();
