<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('object_type', 50);
            $table->integer('object_id')->unsigned();
            $table->string('type', 50)->index();
            $table->string('disk', 20);
            $table->string('dirname', 100);
            $table->string('basename', 100);
            $table->string('mimetype', 100);
            $table->bigInteger('filesize')->unsigned();
            $table->bigInteger('filesize_disk')->unsigned();
            $table->text('data');
            $table->string('visibility', 20);
            $table->string('status', 20)->index();
            $table->nullableTimestamps();

            $table->index(['object_type', 'object_id'], 'object_ref');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('attachments');
    }
}
