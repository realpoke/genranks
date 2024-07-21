<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateImageSymlink extends Command
{
    protected $signature = 'images:link';

    protected $description = 'Create a symbolic link from public/storage/images to resources/images';

    public function handle()
    {
        $targetFolder = resource_path('images');
        $linkFolder = public_path('storage/images');

        $this->info('Creating symbolic link from [resources/images] to [public/storage/images]...');

        if (file_exists($linkFolder)) {
            $this->error('The "public/storage/images" directory already exists.');

            return;
        }

        if (! file_exists(public_path('storage'))) {
            File::makeDirectory(public_path('storage'));
        }

        $this->laravel->make('files')->link($targetFolder, $linkFolder);

        $this->info('The [public/storage/images] directory has been linked to [resources/images].');
    }
}
