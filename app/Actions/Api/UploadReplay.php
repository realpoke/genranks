<?php

namespace App\Actions\Api;

use App\Contracts\Api\UploadsReplayContract;
use App\Http\Requests\Api\UploadReplayRequest;
use App\Jobs\ProcessReplay;
use App\Traits\JsonResponses;
use Illuminate\Http\JsonResponse;

class UploadReplay implements UploadsReplayContract
{
    use JsonResponses;

    public function __invoke(UploadReplayRequest $request): JsonResponse
    {
        $user = $request->user();

        $file = $request->file('replay');
        $fileName = $user->id.'_'.time().'_replay';
        $file->storeAs('', $fileName, 'replays');

        ProcessReplay::dispatch($user, $fileName);

        return $this->success('File uploaded.', ['replay' => $file->getClientOriginalName()]);
    }
}
