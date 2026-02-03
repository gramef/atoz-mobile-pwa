<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeClientReferenceOptionalForTranslatorJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('translator_jobs', function (Blueprint $table) {
            $table->string('client_reference')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('translator_jobs', function (Blueprint $table) {
            $table->string('client_reference')->nullable(false)->change();
        });
    }
}
