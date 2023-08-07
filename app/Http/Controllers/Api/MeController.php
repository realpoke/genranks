<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Traits\JsonResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeController extends Controller
{
    use JsonResponses;

    public function __invoke(Request $request): JsonResponse
    {
        return $this->success(['me' => new UserResource($request->user())]);
    }
}
