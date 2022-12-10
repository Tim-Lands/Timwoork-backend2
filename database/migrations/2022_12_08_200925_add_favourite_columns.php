<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFavouriteColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('favourites', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::table('favourites', function (Blueprint $table) {
            $table->dropForeign('favourites_user_id_foreign');
            $table->dropIndex('favourites_user_id_index');
            $table->dropColumn('user_id');

            $table->dropForeign('favourites_portfolio_item_id_foreign');
            $table->dropIndex('favourites_portfolio_item_id_index');
            $table->dropColumn('portfolio_item_id');
        });
    }
}
