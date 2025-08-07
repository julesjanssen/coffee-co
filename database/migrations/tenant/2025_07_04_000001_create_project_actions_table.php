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
        Schema::create('project_actions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('game_session_id')->index();
            $table->unsignedBigInteger('project_id')->index();
            $table->string('type', 20)->index();
            $table->json('details');
            $table->unsignedTinyInteger('round_id')->nullable();
        });
    }
};
