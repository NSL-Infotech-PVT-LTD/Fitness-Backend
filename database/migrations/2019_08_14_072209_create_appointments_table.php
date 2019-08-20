<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAppointmentsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('appointments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('service_id')->unsigned()->index();
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->date('date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('comments')->nullable();
            $table->enum('status', ['accepted', 'rejected', 'hold'])->default('hold');

            $table->bigInteger('salon_user_id')->unsigned()->nullable();
            $table->foreign('salon_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('customer_user_id')->unsigned()->nullable();
            $table->foreign('customer_user_id')->references('id')->on('users')->onDelete('cascade');
            \App\Helpers\DbExtender::defaultParams($table);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('appointments');
    }

}
