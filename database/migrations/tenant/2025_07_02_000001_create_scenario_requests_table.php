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
        Schema::create('scenario_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('scenario_id')->index();
            $table->unsignedInteger('client_id')->index();
            $table->text('description');
            $table->unsignedTinyInteger('delay')->index();
            $table->unsignedTinyInteger('duration');
            $table->json('requirements');
            $table->json('settings');
        });
    }
};
