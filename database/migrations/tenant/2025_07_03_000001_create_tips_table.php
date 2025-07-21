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
        Schema::create('tips', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('game_session_id')->index();
            $table->unsignedTinyInteger('round_id')->nullable();
            $table->unsignedInteger('tip_id')->index();
        });
    }
};
