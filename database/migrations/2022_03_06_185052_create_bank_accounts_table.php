<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained();
            $table->foreignId('wise_country_id')->nullable()->constrained();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->text('bank_adress')->nullable();
            $table->string('bank_swift')->nullable();
            $table->string('bank_iban')->nullable();
            $table->string('bank_number_account')->nullable();
            $table->string('country_code_phone')->nullable();
            $table->string('phone_number_without_code')->nullable();
            $table->string('city')->nullable();
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
        Schema::dropIfExists('bank_accounts');
    }
}
