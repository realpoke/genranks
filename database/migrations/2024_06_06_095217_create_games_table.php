<?php

use App\Enums\GameStatus;
use App\Enums\GameType;
use App\Models\Map;
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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->enum('status', GameStatus::values())->default(GameStatus::AWAITING);
            $table->string('hash');
            $table->json('summary');
            $table->json('meta');
            $table->json('players');
            $table->enum('type', GameType::values())->default(GameType::UNSUPPORTED);
            $table->foreignIdFor(Map::class)->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
