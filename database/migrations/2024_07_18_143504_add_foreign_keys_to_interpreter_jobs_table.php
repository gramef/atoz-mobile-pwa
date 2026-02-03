<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddForeignKeysToInterpreterJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('interpreter_jobs', function (Blueprint $table) {
        

        //   $table->unsignedInteger('interpreter_type_id')->after('skill_id');
        //     // Add security_type_id column
        //   $table->unsignedInteger('security_type_id')->after('interpreter_type_id');

        //     // Add foreign key constraints
        //     $table->foreign('interpreter_type_id')
        //           ->references('id')->on('interpreter_types')
        //           ->onDelete('restrict');

        //     $table->foreign('security_type_id')
        //           ->references('id')->on('security_types')
        //           ->onDelete('restrict');
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
            $table->dropForeign(['interpreter_type_id']);
            $table->dropForeign(['security_type_id']);

          //  Drop columns
           $table->dropColumn('interpreter_type_id');
           $table->dropColumn('security_type_id');
            //
        });
    }
}
