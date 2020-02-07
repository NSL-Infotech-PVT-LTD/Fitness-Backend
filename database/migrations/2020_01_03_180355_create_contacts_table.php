<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContactsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->text('message')->nullable();
            $table->text('media')->nullable();
            $table->bigInteger('sender_name')->unsigned()->index();
            $table->foreign('sender_name')->references('id')->on('users')->onDelete('cascade');
            \App\Helpers\DbExtender::defaultParams($table);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('contacts');
    }

}
