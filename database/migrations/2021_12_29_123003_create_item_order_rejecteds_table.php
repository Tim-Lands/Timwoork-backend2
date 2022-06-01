<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemOrderRejectedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_order_rejecteds', function (Blueprint $table) {
            $table->id();
            // Relation Item :
            $table->foreignId('item_id')->constrained()
            ->onDelete('CASCADE')
            ->onUpdate('CASCADE');

            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_order_rejecteds');
    }
}
