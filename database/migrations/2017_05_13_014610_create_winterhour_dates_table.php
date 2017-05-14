<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWinterhourDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    //this won't be used -> will be just the dates table
    public function up()
    {
        Schema::create('winterhour_dates', function (Blueprint $table) {
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
        Schema::dropIfExists('winterhour_dates');
    }
}
