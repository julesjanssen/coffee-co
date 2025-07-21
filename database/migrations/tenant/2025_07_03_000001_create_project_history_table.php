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
        Schema::create('project_history', function (Blueprint $table) {
            $table->unsignedInteger('project_id')->index();
            $table->unsignedTinyInteger('round_id')->index();
            $table->string('status', 20)->index();
        });
    }
};
