<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBackgroundPortfolioUrl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profile_sellers', function (Blueprint $table) {
            $table->string('portfolio_cover_url')->default(url("avatars/avatar.png"));
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profile_sellers', function (Blueprint $table) {
            //
        });
    }
}
