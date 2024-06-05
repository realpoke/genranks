<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Api\AuthenticatesUserContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginUserRequest;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    public function __invoke(LoginUserRequest $request, AuthenticatesUserContract $authenticator): JsonResponse
    {
        return $authenticator($request);
    }
}
