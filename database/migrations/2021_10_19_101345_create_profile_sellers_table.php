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
            $table->tinyInteger('steps')->default(0);
            $table->tinyInteger('number_of_sales')->default(0);
            $table->string('portfolio')->nullable();
            $table->text('bio')->nullable();

            // =======================================================
            // relation model of profile
            $table->foreignId('profile_id')->constrained();
            // relation model of Badge
            $table->foreignId('seller_badge_id')->nullable()->constrained();
            // relation model of Country
            $table->foreignId('seller_level_id')->nullable()->constrained();
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
