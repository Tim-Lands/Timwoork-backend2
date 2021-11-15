<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amounts', function (Blueprint $table) {
            $table->id();

            // relation model of Wallet
            $table->foreignId('wallet_id')->constrained()
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');

            $table->float('amount', 5, 2)->default(0);
            $table->float('amount_pending', 5, 2)->default(0);
            $table->tinyInteger('duration_pending');

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
        Schema::dropIfExists('amounts');
    }
}
