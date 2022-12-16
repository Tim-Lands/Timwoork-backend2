<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryForeignKeyToPortfolios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portfolio_items', function (Blueprint $table) {
            $table->foreignId('category_id')
            ->nullable()
            ->constrained()
            ->onDelete('SET NULL')
            ->onUpdate('SET NULL');
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
            $table->dropForeign('portfolio_items_category_id_foreign');
            $table->dropIndex('portfolio_items_category_id_index');
            $table->dropColumn('category_id');
        });
    }
}
