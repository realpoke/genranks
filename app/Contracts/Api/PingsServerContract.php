<?php

namespace App\Contracts\Api;

use Illuminate\Http\JsonResponse;

interface PingsServerContract
{
    public function __invoke(): JsonResponse;
}
