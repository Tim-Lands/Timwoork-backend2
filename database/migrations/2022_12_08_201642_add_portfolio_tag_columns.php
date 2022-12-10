<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPortfolioTagColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portfolio_item_tags', function (Blueprint $table) {
            $table->unsignedBigInteger('tag_id');
            $table->index('tag_id');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');

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
        Schema::table('portfolio_item_tags', function (Blueprint $table) {
            $table->dropForeign('portfolio_item_tag_tag_id_foreign');
            $table->dropIndex('portfolio_item_tag_tag_id_index');
            $table->dropColumn('tag_id');

            $table->dropForeign('portfolio_item_tag_portfolio_item_id_foreign');
            $table->dropIndex('portfolio_item_tag_portfolio_item_id_index');
            $table->dropColumn('portfolio_item_id');
        });
    }
}
