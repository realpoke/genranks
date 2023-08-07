<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UploadGameRequest;
use App\Jobs\Api\ProcessUploadedGame;
use App\Traits\JsonResponses;

class UploadGameController extends Controller
{
    use JsonResponses;

    public function __invoke(UploadGameRequest $request)
    {
        $user = $request->user();

        $file = $request->file('file');
        $fileName = time().'_'.$file->getClientOriginalName();
        $filePath = $file->storeAs('uploads', $fileName, 'public');

        ProcessUploadedGame::dispatch($user, $filePath);

        return $this->success('File uploaded and queued for processing.');
    }
}
