<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDateOfBirthToDateOnInterpreterJobsTable extends Migration
{
    public function up()
    {
        Schema::table('interpreter_jobs', function (Blueprint $table) {
            $table->date('date_of_birth')->change();
        });
    }

    public function down()
    {
        Schema::table('interpreter_jobs', function (Blueprint $table) {
            $table->string('date_of_birth')->change();
        });
    }
}
