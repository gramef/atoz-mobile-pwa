<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMatchedAgentIdToCancellationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cancellations', function (Blueprint $table) {
            $table->integer('matched_agent_id')->after('id')->nullable();
            $table->integer('job_id')->nullable()->change();
            $table->string('job_type')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cancellations', function (Blueprint $table) {
            $table->dropColumn('matched_agent_id');
            $table->integer('job_id')->nullable(false)->change();
            $table->string('job_type')->nullable(false)->change();
        });
    }
}
