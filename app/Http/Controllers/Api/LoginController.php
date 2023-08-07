<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\JsonResponses;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    use JsonResponses;

    public function __invoke(LoginUserRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (is_null($user) || ! Hash::check($request->password, $user->password)) {
            return $this->error('Credentials does not match!', code: 401);
        }

        return $this->success('You successfully logged in.', [
            'me' => new UserResource($user),
            'token' => $user->createToken($request->token)->plainTextToken,
        ]);
    }
}
