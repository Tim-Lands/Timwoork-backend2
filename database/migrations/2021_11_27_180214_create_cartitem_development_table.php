<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartitemDevelopmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cartitem_development', function (Blueprint $table) {
            $table->id();
            // relation model of SubCart
            $table->foreignId('cart_item_id')->constrained()
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
        Schema::dropIfExists('cartitem_development');
    }
}
