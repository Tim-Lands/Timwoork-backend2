<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUrlAndCompletedDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portfolio_items', function (Blueprint $table) {
            $table->string('url');
            $table->date('completed_date')->nullable(true);
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
            $table->dropColumn('url');
            $table->dropColumn('completed_data');
        });
    }
}
