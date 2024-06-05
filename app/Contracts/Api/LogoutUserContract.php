<?php

namespace App\Contracts\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface LogoutUserContract
{
    public function __invoke(Request $request): JsonResponse;
}
