<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemOrderResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_order_resources', function (Blueprint $table) {
            $table->id();
            // Relation Item
            $table->foreignId('item_id')->constrained()
            ->onDelete('CASCADE')
            ->onUpdate('CASCADE');
            // path resource:
            $table->string('path');
            // full path resource:
            $table->string('full_path');
            // size file:
            $table->string('size');
            // type file:
            $table->string('mime_type');
            // status : 0 => delivery 1 => accepted 2 => rejected
            $table->tinyInteger('status')->nullable();
            // added duration in case request:
            $table->integer('duration')->default(0);
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
        Schema::dropIfExists('item_order_resources');
    }
}
