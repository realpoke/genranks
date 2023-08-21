<?php

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
            $table->string('nickname')->unique();
            $table->string('email')->unique();
            $table->mediumInteger('rank')->unsigned()->nullable();
            $table->mediumInteger('monthly_rank')->unsigned()->nullable();
            $table->smallInteger('elo')->unsigned()->default(1500);
            $table->smallInteger('monthly_elo')->unsigned()->default(1500);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->dateTime('claimed_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
