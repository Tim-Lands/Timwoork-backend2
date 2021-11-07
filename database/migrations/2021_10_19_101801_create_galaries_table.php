<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('galaries', function (Blueprint $table) {
            $table->id();
            $table->string('path')->nullable();
            $table->string('full_path')->nullable();
            $table->string('url_video')->nullable();
            $table->string('size')->nullable();
            // type file => pdf , photo
            $table->string('type_file')->nullable();
            $table->string('mime_type')->nullable();
            $table->foreignId('product_id')->constrained()
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
        Schema::dropIfExists('galaries');
    }
}
