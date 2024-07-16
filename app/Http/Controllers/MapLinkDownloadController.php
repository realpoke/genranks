<?php

namespace App\Http\Controllers;

use App\Models\Map;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class MapLinkDownloadController extends Controller
{
    public function __invoke(Request $request, Map $map): string
    {
        $path = $map->path();
        if (! $path || ! Storage::disk('maps')->exists($path)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        return Storage::disk('maps')->path($path, $map->name);
    }
}
