<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBansAddTranslate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bans', function (Blueprint $table) {
            $table->text('comment_ar')->nullable();
            $table->text('comment_en')->nullable();
            $table->text('comment_fr')->nullable();
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
        Schema::table('bans', function (Blueprint $table) {
            $table->dropColumn('comment_ar');
            $table->dropColumn('comment_en');
            $table->dropColumn('comment_fr');
            //
        });
    }
}
