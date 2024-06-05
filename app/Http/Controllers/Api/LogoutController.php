<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Api\LogoutUserContract;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function __invoke(Request $request, LogoutUserContract $logout): JsonResponse
    {
        return $logout($request);
    }
}
