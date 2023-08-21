<?php

namespace App\Http\Controllers;

use App\Actions\GetLatestGenLinkDownloadLink;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GenLinkDownloadController extends Controller
{
    public function __invoke(Request $request, GetLatestGenLinkDownloadLink $downloader)
    {
        return redirect($downloader->downloadLink() ?? abort(Response::HTTP_NOT_FOUND));
    }
}
