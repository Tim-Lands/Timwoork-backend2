<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            // relation model of Cart
            $table->foreignId('cart_id')->constrained()
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');
            // relation model of Product
            $table->foreignId('product_id')->constrained()
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');
            $table->string('product_title');
            $table->float('price_product', 8, 2)->unsigned()->default(0);
            $table->integer('quantity')->default(1);
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
        Schema::dropIfExists('cart_items');
    }
}
