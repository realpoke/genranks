<?php

namespace App\Http\Controllers;

use App\Actions\GetLatestGenLinkDownloadLink;
use Symfony\Component\HttpFoundation\Response;

class GenLinkDownloadController extends Controller
{
    public function __invoke(GetLatestGenLinkDownloadLink $downloader)
    {
        return redirect($downloader() ?? abort(Response::HTTP_NOT_FOUND));
    }
}
