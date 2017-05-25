<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExercisImageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    //this migration is not necessary
    public function up()
    {
        Schema::create('exercise_image', function (Blueprint $table) {
            $table->increments('id');
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
        Schema::dropIfExists('exercise_image');
    }
}
