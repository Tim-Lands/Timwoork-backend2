<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShortenersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shorteners', function (Blueprint $table) {
            $table->id();
            // code of product
            $table->string('code');
            // url of product
            $table->string('url');

            // relation model of Product
            $table->foreignId('product_id')->constrained()
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
        Schema::dropIfExists('shorteners');
    }
}
