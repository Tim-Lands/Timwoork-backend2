<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTranslateTitleToConversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('conversations', function (Blueprint $table) {
            //
            $table->string('title_ar',200)->nullable();
            $table->string('title_en',200)->nullable();
            $table->string('title_fr',200)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('conversations', function (Blueprint $table) {
            //
            $table->dropColumn('title_ar');
            $table->dropColumn('title_en');
            $table->dropColumn('title_fr');
        });
    }
}
