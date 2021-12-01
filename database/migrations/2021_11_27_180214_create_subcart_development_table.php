<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubcartDevelopmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subcart_development', function (Blueprint $table) {
            $table->id();
            // relation model of SubCart
            $table->foreignId('sub_cart_id')->constrained()
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
        Schema::dropIfExists('subcart_development');
    }
}
