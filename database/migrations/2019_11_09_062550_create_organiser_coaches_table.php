<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrganiserCoachesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organiser_coaches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('profile_image');
            $table->text('sport_id')->nullable();
            $table->bigInteger('organisation_id')->unsigned()->index();
            $table->foreign('organisation_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('bio')->nullable();
            $table->integer('hourly_rate')->nullable();
            $table->text('experience_detail')->nullable();
            $table->float('expertise_years')->nullable();
            $table->string('profession')->nullable();
            $table->string('training_service_detail')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('organiser_coaches');
    }
}
