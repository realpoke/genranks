<?php

namespace App\Contracts\Api;

use App\Http\Requests\Api\UploadReplayRequest;
use Illuminate\Http\JsonResponse;

interface UploadsReplayContract
{
    public function __invoke(UploadReplayRequest $request): JsonResponse;
}
