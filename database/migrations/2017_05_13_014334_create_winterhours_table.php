<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWinterhoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('winterhours', function (Blueprint $table) {
            $table->increments('id');
            $table->string('day');
            $table->time('time');
            $table->integer('amount_of_courts');
            $table->tinyInteger('mixed_doubles');
            $table->integer('made_by')->unsigned()->nullable();
            $table->foreign('made_by')->references('id')->on('users');
            $table->softDeletes();
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
        Schema::dropIfExists('winterhours');
    }
}
