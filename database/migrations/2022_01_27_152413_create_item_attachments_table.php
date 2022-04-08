<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_attachments', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->string('path', 255)->nullable();
            $table->string('size')->nullable();
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
        Schema::dropIfExists('item_attachments');
    }
}
