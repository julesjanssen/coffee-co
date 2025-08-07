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
        Schema::create('game_campaigncodes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('game_session_id')->index();
            $table->unsignedBigInteger('participant_id')->index()->nullable();
            $table->unsignedBigInteger('code_id')->index();
            $table->json('details');
            $table->unsignedTinyInteger('round_id')->nullable();
        });
    }
};
