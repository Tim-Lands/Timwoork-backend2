<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToPortfolioViews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portfolio_views', function (Blueprint $table) {
            $table->unsignedBigInteger('profile_id');
            $table->index('profile_id');
            $table->foreign('profile_id')->references('id')->on('profiles')->onDelete('cascade');

            $table->unsignedBigInteger('portfolio_item_id');
            $table->index('portfolio_item_id');
            $table->foreign('portfolio_item_id')->references('id')->on('portfolio_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('portfolio_views', function (Blueprint $table) {
            $table->dropColumn('profile_id');
            $table->dropColumn('portfolio_item_id');
        });
    }
}
