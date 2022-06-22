<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsTitleEnFrToProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('title_en')->nullable();
            $table->string('title_fr')->nullable();
            $table->string('slug_en')->nullable();
            $table->string('slug_fr')->nullable();
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
            $table->dropColumn('title_en');
            $table->dropColumn('title_fr');
            $table->dropColumn('slug_en');
            $table->dropColumn('slug_fr');
        });
    }
}
