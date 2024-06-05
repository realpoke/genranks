<?php

namespace App\Actions;

use App\Contracts\GetsMarkdownFileContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class GetMarkdownFile implements GetsMarkdownFileContract
{
    public function __invoke(string $name): string
    {
        $localName = $name.'_'.app()->getLocale();

        $path = Arr::first([
            resource_path('markdown/'.$localName.'.md'),
            resource_path('markdown/'.$name.'.md'),
        ], function ($path) {
            return file_exists($path);
        });

        abort_if($path == false, 404);

        return Str::markdown(file_get_contents($path));
    }
}
