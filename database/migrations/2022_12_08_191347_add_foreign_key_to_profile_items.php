<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToProfileItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portfolio_items', function (Blueprint $table) {
            $table->unsignedBigInteger('seller_id')->nullable(false);
            $table->text('content')->nullable(false);
            $table->index('seller_id');
            $table->foreign('seller_id')->references('id')->on('profile_sellers')->onDelete('cascade');
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
        Schema::table('portfolio_items', function (Blueprint $table) {
            $table->dropForeign('portfolio_items_seller_id_foreign');
            $table->dropIndex('portfolio_items_seller_id_index');
            $table->dropColumn('seller_id');
            $table->dropColumn('content');
        });
    }
}
