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
            $table->time('business_hour')->nullable();
            $table->date('date')->nullable();
            $table->float('hourly_rate')->nullable();
            $table->text('images')->nullable();
            $table->string('phone')->nullable();
            $table->integer('max_occupancy')->nullable();
            if (\App::VERSION() >= '5.8') {
                $table->bigInteger('created_by')->unsigned()->index();
            } else {
                $table->integer('created_by')->unsigned()->index();
            }
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
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
