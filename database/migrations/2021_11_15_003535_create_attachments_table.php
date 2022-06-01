<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->text('instructions');
            $table->string('path')->nullable();
            $table->string('full_path')->nullable();
            $table->string('size')->nullable();
            // type file => pdf , photo
            $table->string('type_file')->nullable();
            $table->string('mime_type')->nullable();
            // relation model of Item
            $table->foreignId('item_id')->constrained()
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attachments');
    }
}
