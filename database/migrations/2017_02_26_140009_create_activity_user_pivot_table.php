<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityUserPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('signed_up_by')->unsigned()->index();
            $table->foreign('signed_up_by')->references('id')->on('users');
            $table->integer('activity_id')->unsigned()->index();
            $table->foreign('activity_id')->references('id')->on('activities');
            $table->tinyInteger('helper_participant');
            $table->tinyInteger('paid');
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
        Schema::dropIfExists('activity_user');
    }
}
