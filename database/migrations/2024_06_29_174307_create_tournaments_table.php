<?php

use App\Enums\TournamentStatus;
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
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamp('start_at');
            $table->json('stages')->nullable();
            $table->smallInteger('minimum_elo')->unsigned()->default(0);
            $table->tinyInteger('player_slots')->unsigned()->default(8);
            $table->boolean('invite_only')->default(false);
            $table->enum('status', TournamentStatus::values())->default(TournamentStatus::UPCOMING);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
