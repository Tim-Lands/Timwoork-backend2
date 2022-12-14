<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFollowsRelationship extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('follows', function (Blueprint $table) {
            // Edit 2 without an incremental id
         // $table->increments('id');
         $table->unsignedBigInteger('follower_id')->unsigned();
         $table->unsignedBigInteger('following_id')->unsigned();
         $table->foreign('follower_id')->references('id')
               ->on('profiles')
               ->onDelete('cascade');
         $table->foreign('following_id')->references('id')
               ->on('profiles')
               ->onDelete('cascade');

         // Edit 2: with primary and unique constraint
         $table->unique(['follower_id', 'following_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('follows', function (Blueprint $table) {
            $table->dropColumn('following_id');
            $table->dropColumn('follower_id');
        });
    }
}
