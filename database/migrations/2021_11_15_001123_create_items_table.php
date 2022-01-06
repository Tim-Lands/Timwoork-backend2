<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->bigInteger('number_product');
            $table->float('price_product', 8, 2)->nullable();
            // relation model of Order
            $table->foreignId('order_id')->constrained()
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');
            // relation model of Order
            $table->foreignId('profile_seller_id')->constrained()
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');

            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('duration');
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
        Schema::dropIfExists('items');
    }
}
