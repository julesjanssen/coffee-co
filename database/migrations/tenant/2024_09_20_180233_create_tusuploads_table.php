<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTusuploadsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tus_uploads', function (Blueprint $table) {
            $table->string('identifier', 36);
            $table->text('data');
            $table->integer('progress')->unsigned();
            $table->integer('filesize')->unsigned();
            $table->nullableTimestamps();
            $table->timestamp('expires_at')->nullable();

            $table->primary(['identifier'], 'identifier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('tus_uploads');
    }
}
