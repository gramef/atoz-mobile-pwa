<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCancelledAtToJobsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('interpreter_jobs', function (Blueprint $table) {
            $table->timestamp('cancelled_at')->after('updated_at')->nullable();
        });

        Schema::table('translator_jobs', function (Blueprint $table) {
            $table->timestamp('cancelled_at')->after('updated_at')->nullable();
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
            $table->dropColumn('cancelled_at');
        });

        Schema::table('translator_jobs', function (Blueprint $table) {
            $table->dropColumn('cancelled_at');
        });
    }
}
