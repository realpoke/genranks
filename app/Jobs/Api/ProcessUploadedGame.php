<?php

namespace App\Jobs\Api;

use App\Actions\Gentool\CreateGame;
use App\Actions\ReplayParser;
use App\Contracts\Gentool\CreatesGameContract;
use App\Contracts\ReplaysParserContract;
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

    protected CreatesGameContract $creator;

    protected ReplaysParserContract $parser;

    public function __construct(
        User $user,
        string $filePath
    ) {
        $this->user = $user;
        $this->filePath = $filePath;
        $this->creator = new CreateGame();
        $this->parser = new ReplayParser();
    }

    public function handle(): void
    {
        $parsedData = $this->parser->parse($this->filePath);
        dump($parsedData);
        Storage::disk('replays')->delete($this->filePath);
    }
}
