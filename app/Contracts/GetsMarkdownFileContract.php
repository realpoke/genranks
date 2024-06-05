<?php

namespace App\Contracts;

interface GetsMarkdownFileContract
{
    public function __invoke(string $name): string;
}
