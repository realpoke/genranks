<?php

namespace App\Jobs;

use App\Contracts\GenTool\CreatesGenToolUserContract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DownloadReplay implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $file;

    protected int $uniqueId;

    /**
     * Create a new job instance.
     */
    public function __construct(
        string $file,
        int|string $uniqueId
    ) {
        $this->file = $file;
        $this->uniqueId = $uniqueId;
    }

    /**
     * Execute the job.
     */
    public function handle(CreatesGenToolUserContract $userCreator): void
    {
        $this->downloadFileWithDelay($userCreator);
    }

    private function downloadFileWithDelay(CreatesGenToolUserContract $userCreator)
    {
        $user = explode('/', $this->file)[4];
        $user = $userCreator($user)->first();

        $url = "https://www.gentool.net/$this->file";
        $fileName = basename($this->file);

        try {
            $fileContent = file_get_contents($url);
            $fileName = $user->id.'_'.time().'_'.$this->uniqueId.'_replay';
            $uploadedReplay = Storage::disk('replays')->put($fileName, $fileContent);
        } catch (\Throwable $th) {
            Log::error("Failed to upload: $url");

            return;
        }

        if ($uploadedReplay != false) {
            ProcessReplay::dispatch($user, $fileName, anticheat: true);
        }
    }
}
