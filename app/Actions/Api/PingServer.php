<?php

namespace App\Actions\Api;

use App\Contracts\Api\PingsServerContract;
use App\Traits\JsonResponses;
use Illuminate\Http\JsonResponse;

class PingServer implements PingsServerContract
{
    use JsonResponses;

    public function __invoke(): JsonResponse
    {
        return $this->success('Pong.');
    }
}
