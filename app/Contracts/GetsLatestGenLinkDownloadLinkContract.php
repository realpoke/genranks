<?php

namespace App\Contracts;

interface GetsLatestGenLinkDownloadLinkContract
{
    public function __invoke(): ?string;
}
