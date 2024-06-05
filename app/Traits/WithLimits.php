<?php

namespace App\Traits;

use App\Exceptions\TooManyRequestsException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

trait WithLimits
{
    public function limitTo(int $perMinute, string $forField, string $to, int $decaySeconds = 120): void
    {
        try {
            $this->rateLimit($perMinute, $decaySeconds);
        } catch (TooManyRequestsException $exception) {
            throw ValidationException::withMessages([
                $forField => 'Slow down! Please wait another '.$exception->secondsUntilAvailable.' seconds to '.$to.'.',
            ]);
        }
    }

    private function clearRateLimiter($method = null, $component = null): void
    {
        $method ??= debug_backtrace(limit: 2)[1]['function'];

        $component ??= static::class;

        $key = $this->getRateLimitKey($method, $component);

        RateLimiter::clear($key);
    }

    private function getRateLimitKey($method, $component): string
    {
        $method ??= debug_backtrace(limit: 2)[1]['function'];

        $component ??= static::class;

        return sha1($component.'|'.$method.'|'.request()->ip());
    }

    private function hitRateLimiter($method = null, $decaySeconds = 60, $component = null): void
    {
        $method ??= debug_backtrace(limit: 2)[1]['function'];

        $component ??= static::class;

        $key = $this->getRateLimitKey($method, $component);

        RateLimiter::hit($key, $decaySeconds);
    }

    private function rateLimit($maxAttempts, $decaySeconds = 60, $method = null, $component = null): void
    {
        $method ??= debug_backtrace(limit: 2)[1]['function'];

        $component ??= static::class;

        $key = $this->getRateLimitKey($method, $component);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $ip = request()->ip();
            $secondsUntilAvailable = RateLimiter::availableIn($key);

            throw new TooManyRequestsException($component, $method, $ip, $secondsUntilAvailable);
        }

        $this->hitRateLimiter($method, $decaySeconds, $component);
    }
}
