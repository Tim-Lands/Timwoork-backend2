<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPortfolioGalleryColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portfolio_item_gallery', function (Blueprint $table) {
            $table->string('image_url')->default(url("avatars/avatar.png"));
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
        Schema::table('portfolio_item_gallery', function (Blueprint $table) {
            $table->dropForeign('portfolio_item_gallery_portfolio_item_id_foreign');
            $table->dropIndex('portfolio_item_gallery_portfolio_item_id_index');
            $table->dropColumn('portfolio_item_id');
            $table->dropColumn('image_url');

        });
    }
}
