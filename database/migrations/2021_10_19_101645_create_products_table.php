<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->text('content');
            $table->float('price', 5, 2);
            $table->string('duration');
            // $table->string('some_develop');
            $table->text('buyer_instruct');
            $table->boolean('status')->nullable();
            // relation model of ProfileSeller
            $table->bigInteger('profile_seller_id')->unsigned();
            // $table->foreignId('profile_seller_id')->constrained();
            // relation model of Category
            $table->foreignId('category_id')
                ->nullable()
                ->constrained()
                ->onDelete('SET NULL')
                ->onUpdate('SET NULL');

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
        Schema::dropIfExists('products');
    }
}
