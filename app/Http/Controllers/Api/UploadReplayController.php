<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Api\UploadsReplayContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UploadReplayRequest;
use Illuminate\Http\JsonResponse;

class UploadReplayController extends Controller
{
    public function __invoke(UploadReplayRequest $request, UploadsReplayContract $uploader): JsonResponse
    {
        return $uploader($request);
    }
}
