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
        Schema::create('scenario_request_solutions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('scenario_id')->index();
            $table->unsignedInteger('request_id')->index();
            $table->json('product_ids');
            $table->unsignedInteger('score');
            $table->boolean('is_optimal')->index();
        });
    }
};
