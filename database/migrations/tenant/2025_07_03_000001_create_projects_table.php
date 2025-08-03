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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('game_session_id')->index();
            $table->unsignedInteger('request_id')->index();
            $table->unsignedInteger('solution_id')->index()->nullable();
            $table->unsignedInteger('client_id')->index();
            $table->string('status', 20)->index();
            $table->unsignedInteger('price');
            $table->unsignedTinyInteger('failure_chance');
            $table->unsignedTinyInteger('downtime');
            $table->string('location', 20);
            $table->json('settings');
            $table->unsignedTinyInteger('request_round_id')->nullable();
            $table->unsignedTinyInteger('quote_round_id')->nullable();
            $table->unsignedTinyInteger('delivery_round_id')->nullable();
            $table->unsignedTinyInteger('down_round_id')->nullable();
            $table->timestamps();
        });
    }
};
