<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAgentIdToInterpreterAndTranslatorJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('interpreter_jobs', function (Blueprint $table) {
            $table->integer('agent_id')->nullable();
        });
        
        Schema::table('translator_jobs', function (Blueprint $table) {
            $table->integer('agent_id')->nullable();
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
            $table->dropColumn('agent_id');
        });

        Schema::table('translator_jobs', function (Blueprint $table) {
            $table->dropColumn('agent_id');
        });
    }
}
