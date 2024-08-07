<?php

namespace App\Providers;

use App\Actions\Auth\AuthenticateUser;
use App\Actions\Auth\CreateNewUser;
use App\Actions\Auth\LogoutUser;
use App\Actions\Auth\Option\DeleteToken;
use App\Actions\Auth\Option\DeleteUser;
use App\Actions\Auth\Option\LogoutSessions;
use App\Actions\Auth\Option\SetRankMode;
use App\Actions\Auth\Option\UpdatePassword;
use App\Actions\Auth\Option\UpdateUser;
use App\Actions\Auth\Password\ConfirmPassword;
use App\Actions\Auth\Password\ResetUserPassword;
use App\Actions\Auth\Password\SendPasswordResetLink;
use App\Actions\Auth\SendEmailVerification;
use App\Actions\Auth\VerifyEmail;
use App\Actions\CalculateElo;
use App\Actions\Clan\CreateClan;
use App\Actions\CreateMapHash;
use App\Actions\GenTool\CreateGenToolUser;
use App\Actions\GenTool\GetGenToolUsers;
use App\Actions\GenTool\GetValidGenToolGames;
use App\Actions\GenTool\SearchForGenToolUser;
use App\Actions\GetLatestGenLinkDownloadLink;
use App\Actions\GetMarkdownFile;
use App\Actions\GiveUserElo;
use App\Actions\GiveUserStats;
use App\Actions\ReplayParser;
use App\Actions\UpdateArmyMatchup;
use App\Actions\ValidateGame;
use App\Contracts\Auth\AuthenticatesUserContract;
use App\Contracts\Auth\CreatesNewUserContract;
use App\Contracts\Auth\LogoutUserContract;
use App\Contracts\Auth\Option\DeletesTokenContract;
use App\Contracts\Auth\Option\DeletesUserContract;
use App\Contracts\Auth\Option\LogoutSessionsContract;
use App\Contracts\Auth\Option\SetsRankModeContract;
use App\Contracts\Auth\Option\UpdatesPasswordContract;
use App\Contracts\Auth\Option\UpdatesUserContract;
use App\Contracts\Auth\Password\ConfirmsPasswordContract;
use App\Contracts\Auth\Password\ResetsUserPasswordContract;
use App\Contracts\Auth\Password\SendsPasswordResetLinkContract;
use App\Contracts\Auth\SendsEmailVerificationContract;
use App\Contracts\Auth\VerifyEmailContract;
use App\Contracts\CalculatesEloContract;
use App\Contracts\Clan\CreatesClanContract;
use App\Contracts\CreatesMapHashContract;
use App\Contracts\GenTool\CreatesGenToolUserContract;
use App\Contracts\GenTool\GetsGenToolUsersContract;
use App\Contracts\GenTool\GetsValidGenToolGamesContract;
use App\Contracts\GenTool\SearchesForGenToolUserContract;
use App\Contracts\GetsLatestGenLinkDownloadLinkContract;
use App\Contracts\GetsMarkdownFileContract;
use App\Contracts\GivesUserEloContract;
use App\Contracts\GivesUserStatsContract;
use App\Contracts\ParsesReplayContract;
use App\Contracts\UpdatesArmyMatchupContract;
use App\Contracts\ValidatesGameContract;
use App\Policies\NotificationPolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthenticatesUserContract::class, Authenticateuser::class);
        $this->app->bind(CreatesNewUserContract::class, CreateNewUser::class);
        $this->app->bind(SendsEmailVerificationContract::class, SendEmailVerification::class);
        $this->app->bind(SendsPasswordResetLinkContract::class, SendPasswordResetLink::class);
        $this->app->bind(ConfirmsPasswordContract::class, ConfirmPassword::class);
        $this->app->bind(UpdatesUserContract::class, UpdateUser::class);
        $this->app->bind(SetsRankModeContract::class, SetRankMode::class);
        $this->app->bind(UpdatesPasswordContract::class, UpdatePassword::class);
        $this->app->bind(DeletesUserContract::class, DeleteUser::class);
        $this->app->bind(LogoutSessionsContract::class, LogoutSessions::class);
        $this->app->bind(LogoutUserContract::class, LogoutUser::class);
        $this->app->bind(ResetsUserPasswordContract::class, ResetUserPassword::class);
        $this->app->bind(DeletesTokenContract::class, DeleteToken::class);
        $this->app->bind(GetsMarkdownFileContract::class, GetMarkdownFile::class);
        $this->app->bind(VerifyEmailContract::class, VerifyEmail::class);

        $this->app->bind(GetsLatestGenLinkDownloadLinkContract::class, GetLatestGenLinkDownloadLink::class);

        $this->app->bind(ParsesReplayContract::class, ReplayParser::class);
        $this->app->bind(ValidatesGameContract::class, ValidateGame::class);
        $this->app->bind(CalculatesEloContract::class, CalculateElo::class);
        $this->app->bind(CreatesMapHashContract::class, CreateMapHash::class);
        $this->app->bind(GivesUserEloContract::class, GiveUserElo::class);

        $this->app->bind(GetsGenToolUsersContract::class, GetGenToolUsers::class);
        $this->app->bind(CreatesGenToolUserContract::class, CreateGenToolUser::class);
        $this->app->bind(GetsValidGenToolGamesContract::class, GetValidGenToolGames::class);
        $this->app->bind(SearchesForGenToolUserContract::class, SearchForGenToolUser::class);
        $this->app->bind(GivesUserStatsContract::class, GiveUserStats::class);

        $this->app->bind(CreatesClanContract::class, CreateClan::class);

        $this->app->bind(UpdatesArmyMatchupContract::class, UpdateArmyMatchup::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(DatabaseNotification::class, NotificationPolicy::class);

        Builder::macro('whereLike', function (string|array $attributes, string $searchTerm): Builder {
            return $this->where(function (Builder $query) use ($attributes, $searchTerm): void {
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

        Builder::macro('whereJsonHas', function ($column, $values) {
            if (! is_array($values)) {
                $values = [$values];
            }

            $valuesJson = json_encode($values);

            return $this->whereRaw(
                'JSON_CONTAINS(?, ?, 0) AND JSON_CONTAINS(?, ?, 0)',
                [$column, $valuesJson, $valuesJson, $column]
            );
        });
    }
}
