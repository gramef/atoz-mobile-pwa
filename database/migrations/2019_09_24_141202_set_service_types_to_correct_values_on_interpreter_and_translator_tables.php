<?php

use App\InterpreterJob;
use App\TranslatorJob;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class SetServiceTypesToCorrectValuesOnInterpreterAndTranslatorTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        InterpreterJob::where('service_type', 0)->update(['service_type' => 1]);
        InterpreterJob::where('service_type', 1)->update(['service_type' => 3]);
        InterpreterJob::where('service_type', 3)->update(['service_type' => 4]);

        Schema::table('interpreter_jobs', function(Blueprint $table) {
            $table->renameColumn('service_type', 'skill_id');
        });

        TranslatorJob::where('service_type', 0)->update(['service_type' => 7]);
        TranslatorJob::where('service_type', 1)->update(['service_type' => 8]);
        TranslatorJob::where('service_type', 2)->update(['service_type' => 9]);

        Schema::table('translator_jobs', function(Blueprint $table) {
            $table->renameColumn('service_type', 'skill_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        InterpreterJob::where('service_type', 1)->update(['service_type' => 0]);
        InterpreterJob::where('service_type', 3)->update(['service_type' => 1]);
        InterpreterJob::where('service_type', 4)->update(['service_type' => 3]);

        Schema::table('interpreter_jobs', function(Blueprint $table) {
            $table->renameColumn('skill_id', 'service_type');
        });

        TranslatorJob::where('service_type', 7)->update(['service_type' => 0]);
        TranslatorJob::where('service_type', 8)->update(['service_type' => 1]);
        TranslatorJob::where('service_type', 9)->update(['service_type' => 2]);

        Schema::table('translator_jobs', function(Blueprint $table) {
            $table->renameColumn('skill_id', 'service_type');
        });
    }
}
