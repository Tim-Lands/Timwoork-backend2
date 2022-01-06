<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->text('content')->nullable();
            $table->float('price', 8, 2)->nullable();
            $table->tinyInteger('duration')->nullable();
            $table->integer('count_buying')->default(0);
            // nullable sometime
            $table->string('thumbnail')->nullable();

            // $table->string('some_develop');
            $table->text('buyer_instruct')->nullable();
            // حالة الخدمة
            $table->boolean('status')->nullable();
            // اذا كانت الخدمة معطلة ام لا
            $table->boolean('is_active')->nullable();
            // المراحل
            $table->tinyInteger('current_step')->default(0);
            // اكتمال انشاء الخدمة
            $table->tinyInteger('is_completed')->default(0);
            // اذا كان في المسودة ام لا
            $table->tinyInteger('is_draft')->default(0);
            // relation model of ProfileSeller
            // $table->bigInteger('profile_seller_id')->unsigned()->nullable();
            $table->foreignId('profile_seller_id')->constrained()
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');
            // relation model of Category
            $table->foreignId('category_id')
                ->nullable()
                ->constrained()
                ->onDelete('SET NULL')
                ->onUpdate('SET NULL');

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
        Schema::dropIfExists('products');
    }
}
