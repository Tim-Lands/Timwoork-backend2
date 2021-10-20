<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            // names of languages (arabic,english,french)
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('name_fr');

            $table->tinyInteger('numbr_develop')->default(0);
            $table->float('price_develop', 5, 2);
            $table->tinyInteger('numbr_sales')->default(0);
            $table->float('value_bayer', 5, 2);

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
        Schema::dropIfExists('levels');
    }
}
