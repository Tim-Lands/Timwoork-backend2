<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnBuyerInstructLangsToProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('buyer_instruct_ar')->nullable();
            $table->string('buyer_instruct_en')->nullable();
            $table->string('buyer_instruct_fr')->nullable();

            $table->string('content_ar')->nullable();
            $table->string('content_en')->nullable();
            $table->string('content_fr')->nullable();

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
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
}
