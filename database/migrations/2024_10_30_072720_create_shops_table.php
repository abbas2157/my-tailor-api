<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopsTable extends Migration
{
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable();
            $table->string('shop_name')->nullable();
            $table->string('famous_name')->nullable();
            $table->string('city')->nullable();
            $table->text('shop_address')->nullable();
            $table->time('shop_open_time')->nullable();
            $table->time('shop_close_time')->nullable();
            $table->enum('status', ['0', '1'])->default('1');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shops');
    }
}
