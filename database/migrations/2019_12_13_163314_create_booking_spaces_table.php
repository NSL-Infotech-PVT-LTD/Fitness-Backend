<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingSpacesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('booking_spaces', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('booking_id')->unsigned()->index();
            $table->foreign('booking_id')->references('id')->on('bookings');
            $table->date('booking_date')->nullable();
            $table->time('from_time')->nullable();
            $table->time('to_time')->nullable();
            $table->integer('hours')->nullable();
            \App\Helpers\DbExtender::defaultParams($table);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('booking_spaces');
    }

}
