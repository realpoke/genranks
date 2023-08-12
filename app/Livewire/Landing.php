<?php

namespace App\Livewire;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Str;
use Livewire\Component;

class Landing extends Component
{
    public $targetTime;

    public $error = false;

    public function mount()
    {
        app()->make(Kernel::class);
        $schedule = app()->make(Schedule::class);

        $tasks = collect($schedule->events());

        $matches = $tasks->filter(function ($item) {
            return Str::contains($item->command, 'gentool:fetch');
        });

        $event = $matches->first();

        if ($event) {
            $time = $matches->first()->nextRunDate();
        } else {
            $time = now();
            $this->error = true;
        }

        $this->targetTime = $time;
    }
}
