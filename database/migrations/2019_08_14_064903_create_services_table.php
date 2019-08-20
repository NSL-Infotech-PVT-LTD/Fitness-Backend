<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateServicesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('services', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('hours_take')->nullable();
            $table->float('price')->nullable();

            if (\App::VERSION() >= '5.8') {
                $table->bigInteger('salon_user_id')->unsigned()->nullable();
            } else {
                $table->integer('salon_user_id')->unsigned()->nullable();
            }
            $table->foreign('salon_user_id')->references('id')->on('users')->onDelete('cascade');
            \App\Helpers\DbExtender::defaultParams($table);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('services');
    }

}
