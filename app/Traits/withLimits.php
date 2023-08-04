<?php

namespace App\Traits;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Illuminate\Validation\ValidationException;

trait withLimits
{
    use WithRateLimiting;

    public function limitTo(int $perMinute, string $forField, string $to, int $decaySeconds = 120)
    {
        try {
            $this->rateLimit($perMinute, $decaySeconds);
        } catch (TooManyRequestsException $exception) {
            throw ValidationException::withMessages([
                $forField => 'Slow down! Please wait another '.$exception->secondsUntilAvailable.' seconds to '.$to.'.',
            ]);
        }
    }
}
