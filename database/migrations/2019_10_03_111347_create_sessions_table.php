<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSessionsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('sessions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->float('hourly_rate')->nullable();
            $table->string('location')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->text('images_1')->nullable();
            $table->text('images_2')->nullable();
            $table->text('images_3')->nullable();
            $table->text('images_4')->nullable();
            $table->text('images_5')->nullable();
            $table->string('phone')->nullable();
            $table->integer('guest_allowed')->nullable();
            if (\App::VERSION() >= '5.8') {
                $table->bigInteger('created_by')->unsigned()->index();
            } else {
                $table->integer('created_by')->unsigned()->index();
            }
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->integer('guest_allowed_left')->default(0);
            $table->bigInteger('sport_id')->unsigned()->index();
            $table->foreign('sport_id')->references('id')->on('sports')->onDelete('cascade');
            \App\Helpers\DbExtender::defaultParams($table);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('sessions');
    }

}
