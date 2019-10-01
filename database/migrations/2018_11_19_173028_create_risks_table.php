<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRisksTable extends Migration
{

    public function up()
    {
        Schema::create('risks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('malfunction_id');
            $table->string('level');
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('risks');
    }
}
