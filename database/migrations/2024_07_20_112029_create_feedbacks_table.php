<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedbacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('agent_id')->unsigned();
            $table->integer('client_id')->unsigned();
            $table->integer('job_id')->unsigned();
            $table->string('appearance_rating')->nullable();
            $table->string('punctuality')->nullable();
            $table->string('quality_of_interpreting')->nullable();
            $table->string('empathy')->nullable();
            $table->string('comment')->nullable();
            $table->string('status')->nullable()->default('N');
            $table->string('agent_status')->nullable()->default('N');
            $table->timestamps();

            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
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
        Schema::table('feedbacks', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->dropForeign(['client_id']);
            $table->dropForeign(['job_id']);
        });

        Schema::dropIfExists('feedbacks');
    }
}
