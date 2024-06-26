<?php

namespace App\Console\Commands\GenTool;

use App\Jobs\DownloadReplay;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class UploadLatest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gentool:uploadlatest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download .rep replay files from GenTool latest 10 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now()->subMinutes(10)->second(0);
        $now->minute = intval($now->minute / 10) * 10;
        $url = sprintf(
            'https://www.gentool.net/data/zh/logs/%s_%s/%s/uploads_%s%s%s_%s%s%s.yaml.txt',
            $now->year,
            str_pad($now->month, 2, '0', STR_PAD_LEFT),
            str_pad($now->day, 2, '0', STR_PAD_LEFT),
            $now->year,
            str_pad($now->month, 2, '0', STR_PAD_LEFT),
            str_pad($now->day, 2, '0', STR_PAD_LEFT),
            str_pad($now->hour, 2, '0', STR_PAD_LEFT),
            str_pad($now->minute, 2, '0', STR_PAD_LEFT),
            '00'
        );

        $response = Http::get($url);

        if ($response->successful()) {
            $this->info("Downloaded YAML: $url");
            $yamlContent = $response->body();
            $this->processYaml($yamlContent);
        } else {
            $this->error("Failed to download: $url");
        }
    }

    private function processYaml($yamlContent)
    {
        $lines = explode("\n", $yamlContent);
        $files = [];
        $collecting = false;

        foreach ($lines as $line) {
            if (trim($line) == 'files:') {
                $collecting = true;

                continue;
            }

            if ($collecting && Str::endsWith($line, '.rep') && explode('_', explode('/', $line)[5])[1] == '1v1') {
                $file = trim($line, ' -');
                $files[] = $file;
                $this->info("Found file: $file");
            }

            if ($collecting && trim($line) == '') {
                $this->info('Found end of files');
                $collecting = false;
            }
        }

        $delayBetweenDownloads = floor((10 * 60) / max(count($files), 1));
        $this->info('Scheduling '.count($files)." files to download with a delay of $delayBetweenDownloads seconds between each");

        foreach ($files as $index => $file) {
            $delay = $index * $delayBetweenDownloads;
            $this->info("Scheduling download for: $file, delay: $delay");
            DownloadReplay::dispatch($file, $index)->delay(now()->addSeconds($delay));
        }
    }
}
