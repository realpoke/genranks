<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Api\UsersDetailContract;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserDetailController extends Controller
{
    public function __invoke(Request $request, UsersDetailContract $userDetails): JsonResponse
    {
        return $userDetails($request);
    }
}
