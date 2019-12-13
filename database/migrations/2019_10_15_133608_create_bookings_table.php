<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('type',['event','space','session']);
            $table->integer('target_id');
            $table->bigInteger('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('owner_id')->unsigned()->index();
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('tickets')->default(1);
            $table->float('price')->default(0.00);
            $table->enum('status', ['pending', 'accepted', 'rejected',
                'completed','completed_rated'])->nullable();
            $table->text('payment_details')->nullable();

            \App\Helpers\DbExtender::defaultParams($table);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}
