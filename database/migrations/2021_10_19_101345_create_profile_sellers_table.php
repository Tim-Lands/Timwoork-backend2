<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfileSellersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile_sellers', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('numbr_sales')->default(0);
            $table->text('bio')->nullable();
            // =======================================================
            // relation model of Country
            $table->foreignId('profile_id')->constrained();
            // relation model of Badge
            $table->foreignId('badge_id')->constrained();
            // relation model of Country
            $table->foreignId('level_id')->constrained();
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
        Schema::dropIfExists('profile_sellers');
    }
}