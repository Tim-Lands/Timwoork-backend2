<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTranslateToPortfolioItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portfolio_items', function (Blueprint $table) {
            $table->string('content_ar')->nullable(false);
            $table->string('content_en')->nullable(false);
            $table->string('content_fr')->nullable(false);
            $table->string('title_ar')->nullable(false);
            $table->string('title_en')->nullable(false);
            $table->string('title_fr')->nullable(false);
        
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
            $table->dropColumn('content_ar');
            $table->dropColumn('content_en');
            $table->dropColumn('content_fr');
            $table->dropColumn('title_ar');
            $table->dropColumn('title_en');
            $table->dropColumn('title_fr');
        });
    }
}
