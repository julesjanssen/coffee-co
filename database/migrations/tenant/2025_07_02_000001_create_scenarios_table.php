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
        Schema::create('scenarios', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('group_id')->index();
            $table->string('title');
            $table->string('locale', 2)->index();
            $table->json('settings');
            $table->string('status', 10)->index();
            $table->timestamps();
        });
    }
};
