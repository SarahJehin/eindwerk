<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('description');
            $table->string('poster');
            $table->string('extra_url')->nullable();
            $table->dateTime('start');
            $table->dateTime('deadline')->nullable();
            $table->dateTime('end')->nullable();
            $table->string('location');
            $table->double('latitude', 10, 6);
            $table->double('longitude', 10, 6);
            $table->integer('min_participants')->nullable();
            $table->integer('max_participants')->nullable();
            $table->integer('helpers')->nullable();
            $table->decimal('price', 6, 2)->nullable();
            //$table->tinyInteger('youth_adult');
            $table->tinyInteger('is_visible');
            $table->tinyInteger('status');
            $table->integer('category_id')->unsigned()->index();
            $table->foreign('category_id')->references('id')->on('categories');
            $table->integer('made_by_id')->unsigned()->index();
            $table->foreign('made_by_id')->references('id')->on('users');
            $table->integer('owner_id')->unsigned()->index();
            $table->foreign('owner_id')->references('id')->on('users');
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
        Schema::dropIfExists('activities');
    }
}
