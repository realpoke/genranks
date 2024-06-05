<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Api\PingsServerContract;
use App\Http\Controllers\Controller;
use App\Traits\JsonResponses;
use Illuminate\Http\JsonResponse;

class PingController extends Controller
{
    use JsonResponses;

    public function __invoke(PingsServerContract $pinger): JsonResponse
    {
        return $pinger();
    }
}
