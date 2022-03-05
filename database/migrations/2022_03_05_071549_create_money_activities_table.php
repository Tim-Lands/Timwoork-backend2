<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoneyActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('money_activities', function (Blueprint $table) {
            $table->id();

            // relation model of Wallet
            $table->foreignId('wallet_id')->constrained()
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');

            $table->float('amount', 8, 2)->unsigned()->default(0);
            $table->tinyInteger('status')->nullable()->default(0);
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
        Schema::dropIfExists('money_activities');
    }
}
