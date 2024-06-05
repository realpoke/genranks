<?php

namespace App\Contracts\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface UsersDetailContract
{
    public function __invoke(Request $request): JsonResponse;
}
