<?php

namespace App\Contracts;

interface GetsMarkdownFileContract
{
    public function localizedMarkdown(string $name);
}
