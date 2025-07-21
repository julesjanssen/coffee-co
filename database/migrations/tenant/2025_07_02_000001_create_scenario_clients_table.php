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
        Schema::create('scenario_clients', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('scenario_id')->index();
            $table->unsignedTinyInteger('player_id')->index();
            $table->string('title');
            $table->string('segment', 20)->index();
            $table->json('settings');
            $table->unsignedInteger('sortorder');
        });
    }
};
