<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartDevelopmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_development', function (Blueprint $table) {
            $table->id();

            // relation model of Cart
            $table->foreignId('cart_id')->constrained()
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');

            // relation model of Development
            $table->foreignId('development_id')->constrained()
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
        Schema::dropIfExists('cart_development');
    }
}
