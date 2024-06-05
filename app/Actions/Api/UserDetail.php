<?php

namespace App\Actions\Api;

use App\Contracts\Api\UsersDetailContract;
use App\Http\Resources\UserResource;
use App\Traits\JsonResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserDetail implements UsersDetailContract
{
    use JsonResponses;

    public function __invoke(Request $request): JsonResponse
    {
        return $this->success('Your user data.', ['me' => new UserResource($request->user())]);
    }
}
