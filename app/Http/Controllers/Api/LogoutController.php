<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\JsonResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    use JsonResponses;

    public function __invoke(Request $request): JsonResponse
    {
        if (Auth::user()->currentAccessToken()->delete()) {
            return $this->success('You have successfully been logged out and your token has been deleted.');
        }

        return $this->error('Failed to logout!');
    }
}
