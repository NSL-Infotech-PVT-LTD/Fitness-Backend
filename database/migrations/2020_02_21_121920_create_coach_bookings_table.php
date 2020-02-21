<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoachBookingsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('coach_bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('coach_id');
            $table->bigInteger('athlete_id')->unsigned()->index();
            $table->foreign('athlete_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('service_id');
            $table->float('price')->default(0.00);
            $table->enum('status', ['pending', 'accepted', 'rejected',
                'completed', 'completed_rated'])->nullable();
            $table->text('payment_details')->nullable();
            $table->string('payment_id')->nullable();
            \App\Helpers\DbExtender::defaultParams($table);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('coach_bookings');
    }

}
