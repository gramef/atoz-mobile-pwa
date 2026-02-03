<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatchedAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matched_agents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('job_id');
            $table->string('job_type');
            $table->integer('agent_id');
            $table->integer('status')->default(0);
            $table->decimal('distance', 4, 1)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('matched_jobs');
    }
}