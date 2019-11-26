<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configurations', function (Blueprint $table) {
            $table->increments('id');
            $table->text('about_us')->nullable();
            $table->text('terms_and_conditions_organiser')->nullable();
            $table->text('terms_and_conditions_coach')->nullable();
            $table->text('terms_and_conditions_athlete')->nullable();
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
        Schema::drop('configurations');
    }
}
