<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellerLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seller_levels', function (Blueprint $table) {
            $table->id();

            // names of languages (arabic,english,french)
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->string('name_fr')->nullable();
            $table->boolean('reliability')->default(0);
            $table->integer('products_number_max')->nullable();
            $table->integer('number_developments_max')->nullable();
            $table->integer('number_of_sales')->nullable();
            $table->float('price_development_max', 8, 2)->nullable();
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
        Schema::dropIfExists('seller_levels');
    }
}
