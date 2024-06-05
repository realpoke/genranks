<?php

namespace App\Actions\Api\Auth;

use App\Contracts\Api\LogoutUserContract;
use App\Traits\JsonResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutUser implements LogoutUserContract
{
    use JsonResponses;

    public function __invoke(Request $request): JsonResponse
    {
        if ($request->user()->currentAccessToken()->delete()) {
            return $this->success('You have successfully been logged out and your token has been deleted.');
        }

        return $this->error('Failed to logout!');
    }
}
