<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLabelColumnToProductTag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement('SET SESSION sql_require_primary_key=0');
        Schema::table('product_tag', function (Blueprint $table) {
            $table->id();
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
        Schema::table('product_tag', function (Blueprint $table) {
            //
        });
    }
}
