<?php

namespace App\Providers;

use App\Actions\Api\Auth\AuthenticateUser;
use App\Actions\Api\Auth\LogoutUser;
use App\Actions\Api\PingServer;
use App\Actions\Api\UploadReplay;
use App\Actions\Api\UserDetail;
use App\Contracts\Api\AuthenticatesUserContract;
use App\Contracts\Api\LogoutUserContract;
use App\Contracts\Api\PingsServerContract;
use App\Contracts\Api\UploadsReplayContract;
use App\Contracts\Api\UsersDetailContract;
use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(PingsServerContract::class, PingServer::class);
        $this->app->bind(AuthenticatesUserContract::class, AuthenticateUser::class);
        $this->app->bind(UsersDetailContract::class, UserDetail::class);
        $this->app->bind(LogoutUserContract::class, LogoutUser::class);
        $this->app->bind(UploadsReplayContract::class, UploadReplay::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
