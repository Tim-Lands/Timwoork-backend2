<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemDateExpiredTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_date_expired', function (Blueprint $table) {
            $table->id();
            // حقل انتهاء وقت الطلبية في حالة لم يتم قبولها او رفضها
            $table->datetime('date_expired')->nullable();
            // حقل انتهاء وقت طلب الغاء من طرف المشتري
            $table->datetime('date_expired_request_canceled')->nullable();
            // حقل انتهاء وقت طلب التعديل من طرف المشتري
            $table->datetime('date_expired_request_modifier')->nullable();
            // relation by item
            $table->foreignId('item_id')->constrained()->onDelete('CASCADE');
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
        Schema::dropIfExists('item_date_expired');
    }
}
