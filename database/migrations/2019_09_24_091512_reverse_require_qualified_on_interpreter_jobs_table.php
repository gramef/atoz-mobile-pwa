<?php

use App\InterpreterJob;
use Illuminate\Database\Migrations\Migration;

class ReverseRequireQualifiedOnInterpreterJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $jobs = InterpreterJob::all();

        foreach ($jobs as $job) {
            $job->unsetEventDispatcher();
            $job->update(['require_qualified' => !$job->require_qualified]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    { 
        $jobs = InterpreterJob::all();

        foreach ($jobs as $job) {
            $job->update(['require_qualified' => !$job->require_qualified]);
        }
    }
}
