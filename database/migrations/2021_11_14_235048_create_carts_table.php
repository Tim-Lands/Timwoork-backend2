<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();

            // relation model of User
            $table->foreignId('user_id')->constrained()
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');

            // relation model of Product
            $table->foreignId('product_id')->constrained()
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');

            $table->integer('count_product')->unsigned();

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
        Schema::dropIfExists('carts');
    }
}
