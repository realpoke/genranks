<?php

namespace App\Contracts;

interface GetsLatestGenLinkDownloadLinkContract
{
    public function downloadLink(): ?string;
}
