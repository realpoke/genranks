<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class ClearReplays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'replay:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes old replays';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fileNames = Storage::disk('replays')->files();

        foreach ($fileNames as $fileName) {
            $lastModified = Carbon::createFromTimestamp(Storage::disk('replays')->lastModified($fileName));

            if ($lastModified->diffInMinutes(Carbon::now()) > 60) {
                Storage::disk('replays')->delete($fileName);
                $this->info("Deleted: $fileName");
            }
        }

        $this->info('Old replays cleanup completed.');
    }
}
