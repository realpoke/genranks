<?php

namespace App\Actions;

use App\Contracts\GetsLatestGenLinkDownloadLinkContract;
use Illuminate\Support\Facades\Http;

class GetLatestGenLinkDownloadLink implements GetsLatestGenLinkDownloadLinkContract
{
    public function __invoke(): ?string
    {
        $latestRelease = $this->githubLatest();

        if (array_key_exists('message', $latestRelease) && $latestRelease['message'] === 'Not Found') {
            return null;
        }

        return $this->getLatestAssetName($latestRelease) ?? null;
    }

    private function githubLatest()
    {
        $response = Http::get('https://api.github.com/repos/realpoke/genlink/releases/latest');

        return $response->json();
    }

    private function getLatestAssetName(array $release): ?string
    {
        $assets = collect($release['assets']);

        if ($assets->isNotEmpty()) {
            $exeAsset = $assets->first(function ($asset) {
                return pathinfo($asset['name'], PATHINFO_EXTENSION) === 'exe';
            });

            if ($exeAsset) {
                return $exeAsset['browser_download_url'];
            }
        }

        return null;
    }
}
