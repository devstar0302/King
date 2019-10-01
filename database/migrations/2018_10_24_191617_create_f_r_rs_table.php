<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFRRsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_r_rs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('paragraph_id');
            $table->string('type')->nullable();
            $table->string('finding')->nullable();
            $table->string('risk')->nullable();
            $table->string('repair')->nullable();
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
        Schema::dropIfExists('f_r_rs');
    }
}
