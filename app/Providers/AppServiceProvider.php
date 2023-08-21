<?php

namespace App\Providers;

use App\Actions\Auth\AuthenticateUser;
use App\Actions\Auth\CreateNewUser;
use App\Actions\Auth\LogoutUser;
use App\Actions\Auth\Option\DeleteToken;
use App\Actions\Auth\Option\DeleteUser;
use App\Actions\Auth\Option\LogoutSessions;
use App\Actions\Auth\Option\UpdatePassword;
use App\Actions\Auth\Option\UpdateUser;
use App\Actions\Auth\Password\ConfirmPassword;
use App\Actions\Auth\Password\ResetUserPassword;
use App\Actions\Auth\Password\SendPasswordResetLink;
use App\Actions\Auth\SendEmailVerification;
use App\Actions\Auth\VerifyEmail;
use App\Actions\Gentool\CreateGame;
use App\Actions\Gentool\CreatePlaceholderUser;
use App\Actions\Gentool\GetUsers;
use App\Actions\GetMarkdownFile;
use App\Actions\ReplayParser;
use App\Contracts\Auth\AuthenticatesUserContract;
use App\Contracts\Auth\CreatesNewUserContract;
use App\Contracts\Auth\LogoutUserContract;
use App\Contracts\Auth\Option\DeletesTokenContract;
use App\Contracts\Auth\Option\DeletesUserContract;
use App\Contracts\Auth\Option\LogoutSessionsContract;
use App\Contracts\Auth\Option\UpdatesPasswordContract;
use App\Contracts\Auth\Option\UpdatesUserContract;
use App\Contracts\Auth\Password\ConfirmsPasswordContract;
use App\Contracts\Auth\Password\ResetsUserPasswordContract;
use App\Contracts\Auth\Password\SendsPasswordResetLinkContract;
use App\Contracts\Auth\SendsEmailVerificationContract;
use App\Contracts\Auth\VerifiesEmailContract;
use App\Contracts\Gentool\CreatesGameContract;
use App\Contracts\Gentool\CreatesPlaceholderUserContract;
use App\Contracts\Gentool\GetsUsersContract;
use App\Contracts\GetsMarkdownFileContract;
use App\Contracts\ReplaysParserContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthenticatesUserContract::class, AuthenticateUser::class);
        $this->app->bind(CreatesNewUserContract::class, CreateNewUser::class);
        $this->app->bind(SendsEmailVerificationContract::class, SendEmailVerification::class);
        $this->app->bind(SendsPasswordResetLinkContract::class, SendPasswordResetLink::class);
        $this->app->bind(ConfirmsPasswordContract::class, ConfirmPassword::class);
        $this->app->bind(UpdatesUserContract::class, UpdateUser::class);
        $this->app->bind(UpdatesPasswordContract::class, UpdatePassword::class);
        $this->app->bind(DeletesUserContract::class, DeleteUser::class);
        $this->app->bind(LogoutSessionsContract::class, LogoutSessions::class);
        $this->app->bind(LogoutUserContract::class, LogoutUser::class);
        $this->app->bind(ResetsUserPasswordContract::class, ResetUserPassword::class);
        $this->app->bind(DeletesTokenContract::class, DeleteToken::class);
        $this->app->bind(GetsMarkdownFileContract::class, GetMarkdownFile::class);
        $this->app->bind(VerifiesEmailContract::class, VerifyEmail::class);
        $this->app->bind(ReplaysParserContract::class, ReplayParser::class);
        $this->app->bind(CreatesGameContract::class, CreateGame::class);
        $this->app->bind(GetsUsersContract::class, GetUsers::class);
        $this->app->bind(CreatesPlaceholderUserContract::class, CreatePlaceholderUser::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Builder::macro('whereLike', function (array|string $attributes, string $searchTerm): Builder {
            /** @var Builder $this */
            return $this->where(function (Builder $query) use ($attributes, $searchTerm) {
                foreach (Arr::wrap($attributes) as $attribute) {
                    $query->when(
                        str_contains($attribute, '.'),
                        function (Builder $query) use ($attribute, $searchTerm): void {
                            [$relationName, $relationAttribute] = explode('.', $attribute);
                            $query->orWhereHas($relationName, function (Builder $query) use ($relationAttribute, $searchTerm): void {
                                $query->where($relationAttribute, 'LIKE', "%{$searchTerm}%");
                            });
                        },
                        function (Builder $query) use ($attribute, $searchTerm): void {
                            $query->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                        }
                    );
                }
            });
        });
    }
}
