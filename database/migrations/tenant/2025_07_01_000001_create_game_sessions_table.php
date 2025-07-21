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
        Schema::create('game_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('scenario_group_id')->index();
            $table->unsignedBigInteger('scenario_id')->nullable();
            $table->string('public_id', 20)->index();
            $table->string('title');
            $table->unsignedInteger('current_round_id');
            $table->string('round_status', 20);
            $table->string('status', 20);
            $table->json('settings');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
