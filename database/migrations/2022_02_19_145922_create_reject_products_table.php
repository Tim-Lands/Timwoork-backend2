<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRejectProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reject_products', function (Blueprint $table) {
            $table->id();
            $table->string('title_product');
            $table->string("first_name");
            $table->string("last_name");
            $table->string("email");
            $table->text("message_rejected");
            // relation by product
            $table->foreignId('product_id')->constrained()->onDelete('CASCADE');
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
        Schema::dropIfExists('reject_products');
    }
}
