<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();

            // relation model of User
            $table->foreignId('wallet_id')->constrained()
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');

            $table->tinyInteger('type');
            $table->integer('number');
            $table->integer('cvc');
            $table->integer('code_postal');
            $table->tinyInteger('last_four_numbers');
            $table->string('date_expiry');

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
        Schema::dropIfExists('cards');
    }
}
