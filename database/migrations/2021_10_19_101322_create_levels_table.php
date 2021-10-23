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
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->string('name_fr')->nullable();
            //  1 الصنف: مستوى بائع 0 / مستوى المشتري
            $table->tinyInteger('type');
            $table->tinyInteger('number_developments')->nullable();
            $table->float('price_developments', 5, 2)->nullable();
            $table->tinyInteger('number_sales')->nullable();
            $table->float('value_bayer', 5, 2)->nullable();

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
