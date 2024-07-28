<?php

use App\Enums\RankMode;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('rank_mode', RankMode::values())->default(RankMode::ALL);

            $table->smallInteger('weekly_elo')->unsigned()->default(User::DEFAULT_ELO);
            $table->mediumInteger('weekly_rank')->unsigned()->nullable();

            $table->smallInteger('monthly_elo')->unsigned()->default(User::DEFAULT_ELO);
            $table->mediumInteger('monthly_rank')->unsigned()->nullable();

            $table->smallInteger('elo')->unsigned()->default(User::DEFAULT_ELO);
            $table->mediumInteger('rank')->unsigned()->nullable();

            $table->smallInteger('ffa_weekly_elo')->unsigned()->default(User::DEFAULT_ELO);
            $table->mediumInteger('ffa_weekly_rank')->unsigned()->nullable();

            $table->smallInteger('ffa_monthly_elo')->unsigned()->default(User::DEFAULT_ELO);
            $table->mediumInteger('ffa_monthly_rank')->unsigned()->nullable();

            $table->smallInteger('ffa_elo')->unsigned()->default(User::DEFAULT_ELO);
            $table->mediumInteger('ffa_rank')->unsigned()->nullable();

            $table->boolean('fake')->default(false);
            $table->json('stats')->nullable();
            $table->json('gentool_ids')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
    }
};
