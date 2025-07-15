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
        Schema::create('game_facilitators', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('game_session_id')->unsigned()->index();
            $table->string('code', 10);
        });
    }
};
