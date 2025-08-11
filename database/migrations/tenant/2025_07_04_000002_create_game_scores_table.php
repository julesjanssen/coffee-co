<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('game_scores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('game_session_id')->index();
            $table->unsignedBigInteger('participant_id')->index()->nullable();
            $table->unsignedBigInteger('client_id')->index()->nullable();
            $table->string('type', 50)->index();
            $table->string('trigger_type', 100);
            $table->unsignedBigInteger('trigger_id')->nullable();
            $table->string('event', 20)->index();
            $table->json('details');
            $table->unsignedTinyInteger('round_id')->nullable();
            $table->tinyInteger('value');

            $table->index(['trigger_type', 'trigger_id']);
        });
    }
};
