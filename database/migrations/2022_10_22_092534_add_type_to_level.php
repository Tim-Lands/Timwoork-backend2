<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToLevel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('levels', function (Blueprint $table) {
            //
            $table->integer('type')->nullable();
            $table->integer('number_developments')->nullable();
            $table->integer('price_developments')->nullable();
            $table->integer('number_sales')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('levels', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('number_developments');
            $table->dropColumn('price_developments');
            $table->dropColumn('number_sales');
        });
    }
}
