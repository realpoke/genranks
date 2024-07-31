<?php

namespace App\Http\Middleware;

use App\Enums\Language;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->cookie('preferred_language');

        if (is_null($locale)) {
            $browserLocale = $request->getPreferredLanguage(Language::values());

            $locale = in_array($browserLocale, Language::values()) ? $browserLocale : config('app.locale');

            Cookie::queue('preferred_language', $locale);
        } elseif (! in_array($locale, Language::values())) {
            $locale = config('app.locale');
            Cookie::queue('preferred_language', $locale);
        }

        App::setLocale($locale);

        return $next($request);
    }
}
