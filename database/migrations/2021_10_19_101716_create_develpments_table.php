<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevelpmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('develpments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('duration')->nullable();
            // is_duration: 0 => have a time of product , 1 => no any time
            $table->boolean('is_duration')->default(0);

            // relation of Model Product
            $table->foreignId('product_id')->constrained();

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
        Schema::dropIfExists('develpments');
    }
}
