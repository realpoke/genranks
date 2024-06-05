<?php

namespace App\Contracts\Api;

use App\Http\Requests\Api\LoginUserRequest;
use Illuminate\Http\JsonResponse;

interface AuthenticatesUserContract
{
    public function __invoke(LoginUserRequest $request): JsonResponse;
}
