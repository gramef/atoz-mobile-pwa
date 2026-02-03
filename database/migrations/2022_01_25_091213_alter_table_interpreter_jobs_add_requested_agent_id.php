<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableInterpreterJobsAddRequestedAgentId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('interpreter_jobs', function (Blueprint $table) {
            $table->integer('requested_agent_id')->nullable()->after('to_language_id')->unsigned();
            $table->foreign('requested_agent_id')->references('id')->on('agents');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('interpreter_jobs', function (Blueprint $table) {
            $table->dropForeign(['requested_agent_id']);
            $table->dropColumn('requested_agent_id');
        });
    }
}
