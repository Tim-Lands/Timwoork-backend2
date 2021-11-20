<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableConversationUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conversation_users', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');
            // relation model of Conversation
            $table->foreignId('conversation_id')->constrained()
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');
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
        Schema::table('conversation_users', function (Blueprint $table) {
            //
        });
    }
}
