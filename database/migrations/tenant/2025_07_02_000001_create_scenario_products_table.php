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
        Schema::create('scenario_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('scenario_id')->index();
            $table->string('public_id', 10)->index();
            $table->string('type', 50)->index();
            $table->string('material', 20)->index()->nullable();
            $table->string('color', 20)->index()->nullable();
        });
    }
};
