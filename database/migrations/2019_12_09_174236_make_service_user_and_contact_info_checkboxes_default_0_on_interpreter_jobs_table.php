<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeServiceUserAndContactInfoCheckboxesDefault0OnInterpreterJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('interpreter_jobs', function (Blueprint $table) {
            $table->boolean('service_user_required')->default(0)->change();
            $table->boolean('contact_information_is_same_as_account')->default(0)->change();
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
            $table->boolean('service_user_required')->default(null)->change();
            $table->boolean('contact_information_is_same_as_account')->default(null)->change();
        });
    }
}
