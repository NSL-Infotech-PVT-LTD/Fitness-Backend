<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEventsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('events', function (Blueprint $table) {
            if (\App::VERSION() >= '5.8') {
                $table->bigIncrements('id');
            } else {
                $table->increments('id');
            }
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->string('location')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            if (\App::VERSION() >= '5.8') {
                $table->bigInteger('service_id')->unsigned()->index();
            } else {
                $table->integer('service_id')->unsigned()->index();
            }
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            if (\App::VERSION() >= '5.8') {
                $table->bigInteger('organizer_id')->unsigned()->index();
            } else {
                $table->integer('organizer_id')->unsigned()->index();
            }
            $table->foreign('organizer_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('guest_allowed')->default(0);
            $table->string('equipment_required')->nullable();
            \App\Helpers\DbExtender::defaultParams($table);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('events');
    }

}