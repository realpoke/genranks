<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\JsonResponses;

class PingController extends Controller
{
    use JsonResponses;

    public function __invoke()
    {
        return $this->success('Pong.');
    }
}
