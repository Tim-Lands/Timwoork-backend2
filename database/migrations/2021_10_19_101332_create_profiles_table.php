<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string("first_name")->nullable();
            $table->string('last_name')->nullable();
            $table->string('avatar')->default('avatar.png');
            // type => male or female
            $table->boolean('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->float('credit', 5, 2);
            // dark mood => dark od light
            $table->boolean('dark_mode')->default(0);
            // lang => ar or en or fr
            $table->string('lang')->nullable();

            // is_selling => [0 , 1] => 0 : not sellig , 1: is selling
            $table->boolean('is_seller')->default(0);
            $table->tinyInteger('precent_rating')->default(0);

            // ============================================
            // relation model of User
            $table->foreignId('user_id')->constrained();
            // relation model of Country
            $table->foreignId('country_id')->constrained()->nullable();
            // relation model of Badge
            $table->foreignId('badge_id')->constrained()->nullable();
            // relation model of Country
            $table->foreignId('level_id')->constrained()->nullable();

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
        Schema::dropIfExists('profiles');
    }
}