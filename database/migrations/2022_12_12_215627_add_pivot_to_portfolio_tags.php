<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPivotToPortfolioTags extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portfolio_item_tags', function (Blueprint $table) {
            $table->string('label', 100)->nullable();
            $table->string('value', 100)->nullable();
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
            $table->dropColumn('label');
            $table->dropColumn('value');
        });
    }
}
