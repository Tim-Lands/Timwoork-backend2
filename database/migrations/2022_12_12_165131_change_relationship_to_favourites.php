<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeRelationshipToFavourites extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('favourites', function (Blueprint $table) {
            $table->dropForeign('favourites_user_id_foreign');
            $table->dropIndex('favourites_user_id_index');
            $table->dropColumn('user_id');

            $table->unsignedBigInteger('profile_id');
            $table->index('profile_id');
            $table->foreign('profile_id')->references('id')->on('profiles')->onDelete('cascade');
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
            $table->dropForeign('favourites_profile_id_foreign');
            $table->dropIndex('favourites_profile_id_index');
            $table->dropColumn('profile_id');
            $table->unsignedBigInteger('user_id');
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
}
