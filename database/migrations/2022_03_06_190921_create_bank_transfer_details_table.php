<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankTransferDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_transfer_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained();
            $table->foreignId('country_id')->nullable()->constrained();
            $table->string('full_name')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();

            $table->string('country_code_phone')->nullable();
            $table->string('phone_number_without_code')->nullable();

            $table->string('country_code_whatsapp')->nullable();
            $table->string('whatsapp_without_code')->nullable();

            $table->string('address_line_one')->nullable();
            $table->string('address_line_two')->nullable();
            $table->string('code_postal')->nullable();

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
        Schema::dropIfExists('bank_transfer_details');
    }
}
