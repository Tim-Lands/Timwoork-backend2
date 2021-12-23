<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();

            // relation model of User
            $table->foreignId('user_id')->constrained()
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');

            $table->float('total_price', 8, 2)->unsigned()->default(0);

            $table->float('tax', 8, 2)->unsigned()->default(0);

            $table->float('price_with_tax', 8, 2)->unsigned()->default(0);

            $table->boolean('is_buying')->default(0);

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
        Schema::dropIfExists('carts');
    }
}
