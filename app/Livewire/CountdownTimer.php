<?php

namespace App\Livewire;

use DateInterval;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;

class CountdownTimer extends Component
{
    public $targetDateTime;

    public $hasError = false;

    public $counterText = 'Updates in';

    public $doneText = 'Processing';

    #[Computed]
    public function days(): string
    {
        return sprintf('%02d', $this->difference()->d);
    }

    #[Computed]
    public function hours(): string
    {
        return sprintf('%02d', $this->difference()->h);
    }

    #[Computed]
    public function minutes(): string
    {
        return sprintf('%02d', $this->difference()->i);
    }

    #[Computed]
    public function seconds(): string
    {
        return sprintf('%02d', $this->difference()->s);
    }

    #[Computed]
    public function difference(): DateInterval
    {
        return $this->targetDateTime->diff(now());
    }

    public function mount(Carbon $targetDateTime)
    {
        $this->targetDateTime = $targetDateTime;
    }
}
