<?php

use App\Console\Commands\ClearGames;
use App\Console\Commands\ClearReplays;
use App\Console\Commands\GenTool\UploadLatest;
use App\Console\Commands\GenTool\UploadRandomUser;
use App\Http\Middleware\LanguageMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')->group(base_path('routes/auth.php'));
        },
    )
    ->withCommands([
        UploadLatest::class,
        UploadRandomUser::class,
        ClearReplays::class,
        ClearGames::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(LanguageMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
