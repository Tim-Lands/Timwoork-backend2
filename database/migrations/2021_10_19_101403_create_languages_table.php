<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            // names of languages (arabic,english,french)
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->string('name_fr')->nullable();

            // relation model of ProfileSeller
            $table->foreignId('profile_seller_id')->constrained();

            $table->string('level');
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
        Schema::dropIfExists('languages');
    }
}
