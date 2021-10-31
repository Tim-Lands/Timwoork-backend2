<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLanguageProfileSellerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('language_profile_seller', function (Blueprint $table) {
            $table->id();
            // relation model of ProfileSeller
            $table->foreignId('profile_seller_id')->constrained();
            // relation model of Language
            $table->foreignId('language_id')->constrained();

            $table->tinyInteger('level')->default(0);
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
        Schema::dropIfExists('language_profile_seller');
    }
}
