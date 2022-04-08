<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTypePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('type_payments', function (Blueprint $table) {
            $table->id();
            // اسم البوابة
            $table->string('name_ar');
            $table->string('name_en');
            // النسبة
            $table->tinyInteger('precent_of_payment')->unsigned()->default(0);
            // القيمة المضافة
            $table->float('value_of_cent', 8, 2)->unsigned()->default(0);

            // حالة البوابة
            $table->boolean('status')->default(1);

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
        Schema::dropIfExists('type_payments');
    }
}
