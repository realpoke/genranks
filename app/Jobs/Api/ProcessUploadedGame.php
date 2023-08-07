<?php

namespace App\Jobs\Api;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessUploadedGame implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    protected $filePath;

    public function __construct(User $user, string $filePath)
    {
        $this->user = $user;
        $this->filePath = $filePath;
    }

    public function handle(): void
    {
        sleep(10);
        Storage::delete($this->filePath);
    }
}
