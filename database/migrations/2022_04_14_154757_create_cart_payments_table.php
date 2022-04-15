<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_payments', function (Blueprint $table) {
            $table->id();
            // الرسوم
            $table->float('tax', 8, 2);
            // المجموع الكلي
            $table->float('total', 8, 2);
            // المجموع الكلي بعد الضريبة
            $table->float('total_with_tax', 8, 2);
            // Foreign Keys for cart_id
            $table->foreignId('cart_id')->constrained()
                ->onDelete('cascade');
            // Foreign Keys for type_payment_id
            $table->foreignId('type_payment_id')->constrained()
                ->onDelete('cascade');
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
        Schema::dropIfExists('cart_payments');
    }
}
