<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTravelExpencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('travel_expences', function (Blueprint $table) {
            $table->increments('id');
            $table->string('travel_start_time');
            $table->string('travel_end_time')->nullable();
            $table->string('travel_amount')->nullable();
            $table->integer('job_id')->unsigned();
            $table->integer('agent_id')->unsigned();
            $table->date('travel_date');
            $table->string('status')->nullable()->default('Y');

            $table->timestamps();

            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('cascade');
            $table->foreign('job_id')->references('id')->on('interpreter_jobs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('travel_expences', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->dropForeign(['job_id']);
        });
        Schema::dropIfExists('travel_expences');
    }
}
