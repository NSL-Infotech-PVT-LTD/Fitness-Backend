<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserFavouritesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('user_favourites', function (Blueprint $table) {
            $table->increments('id');
            if (\App::VERSION() >= '5.8') {
                $table->bigInteger('client_id')->unsigned()->nullable();
            } else {
                $table->integer('client_id')->unsigned()->nullable();
            }
            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
            if (\App::VERSION() >= '5.8') {
                $table->bigInteger('freelancer_id')->unsigned()->nullable();
            } else {
                $table->integer('freelancer_id')->unsigned()->nullable();
            }
            $table->foreign('freelancer_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('user_favourites');
    }

}
