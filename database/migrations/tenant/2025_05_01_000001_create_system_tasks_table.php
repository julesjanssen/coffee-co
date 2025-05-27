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
        Schema::create('system_tasks', function (Blueprint $table) {
            $table->uuid('id')->unique();
            $table->bigInteger('user_id')->unsigned()->nullable()->index();
            $table->json('payload');
            $table->json('result');
            $table->string('status', 20);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }
};
