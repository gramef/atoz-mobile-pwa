<?php

use App\InterpreterJob;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

class ReplaceDurationWithMinutesAndHoursOnInterpreterJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('interpreter_jobs', function (Blueprint $table) {
            $table->integer('duration_hours')->after('start_time')->nullable();
            $table->integer('duration_minutes')->after('duration_hours')->nullable();
        });

        $jobs = InterpreterJob::all();

        foreach ($jobs as $job) {

            /** Make 'hrs' => 'hr' */
            if (Str::contains($job->duration, 'hrs')) {
                $job->update(['duration' => Str::replaceFirst('hrs', 'hr', $job->duration)]);
            }

            /** Make '7 hr' => '7hr' */
            if (Str::contains($job->duration, ' hr')) {
                $job->update(['duration' => Str::replaceFirst(' hr', 'hr', $job->duration)]);
            }

            /** Make '30 mins' => '30mins' */
            if (Str::contains($job->duration, ' mins')) {
                $job->update(['duration' => Str::replaceFirst(' mins', 'mins', $job->duration)]);
            }

            /** Make '15 mins' => '0hr 15mins' */
            if (!Str::contains($job->duration, 'hr')) {
                $job->update(['duration' => '0hr ' . Str::replaceFirst(' ', '', $job->duration)]);
            }

            /** Make '1 hr' => '1hr 0mins' */
            if (!Str::contains($job->duration, 'mins')) {
                $job->update(['duration' => Str::replaceFirst(' ', '', $job->duration) . ' 0mins']);
            }

            $job->update([
                'duration_hours' => preg_replace('/\D/', '', explode(' ', $job->duration)[0]),
                'duration_minutes' => preg_replace('/\D/', '', explode(' ', $job->duration)[1]),
            ]);
        }

        Schema::table('interpreter_jobs', function (Blueprint $table) {
            $table->dropColumn('duration');
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
            $table->dropColumn('duration_hours');
            $table->dropColumn('duration_minutes');
            $table->string('duration')->nullable();
        });
    }
}
