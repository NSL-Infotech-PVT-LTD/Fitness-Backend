<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSpacesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('spaces', function (Blueprint $table) {
            if (\App::VERSION() >= '5.8') {
                $table->bigIncrements('id');
            } else {
                $table->increments('id');
            }
            $table->string('name')->nullable();
            $table->text('images')->nullable();
            $table->text('description')->nullable();
            $table->integer('price_hourly')->default('0');
            $table->integer('price_daily')->default('0');
            $table->string('availability_week')->nullable();

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
        Schema::drop('spaces');
    }

}
