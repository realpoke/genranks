<?php

namespace App\Actions\Api\Auth;

use App\Contracts\Api\AuthenticatesUserContract;
use App\Http\Requests\Api\LoginUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\JsonResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthenticateUser implements AuthenticatesUserContract
{
    use JsonResponses;

    public function __invoke(LoginUserRequest $request): JsonResponse
    {
        if (! Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return $this->error('Credentials does not match!', code: 401);
        }

        $user = User::where('email', $request->email)->first();

        return $this->success('You successfully logged in.', [
            'me' => new UserResource($user),
            'token' => $user->createToken($request->token)->plainTextToken,
        ]);
    }
}
