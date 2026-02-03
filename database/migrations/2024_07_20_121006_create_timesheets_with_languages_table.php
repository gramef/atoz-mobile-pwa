<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimesheetsWithLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timesheets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id');
            $table->integer('agent_id');
            $table->integer('from_language_id');
            $table->integer('to_language_id');
            $table->string('client_reference');
            $table->date('appointment_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->float('duration_hours', 8, 2);
            $table->float('duration_minutes', 8, 2);
            $table->string('status');
            $table->timestamps();

            $table->index('client_id');
            $table->index('agent_id');
            $table->index('from_language_id');
            $table->index('to_language_id');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timesheets');
    }
}