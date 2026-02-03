<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeAddressFieldsOptionalOnInterpreterJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('interpreter_jobs', function (Blueprint $table) {
            $table->string('address_line_1')->nullable()->change();
            $table->string('county')->nullable()->change();
            $table->string('postcode')->nullable()->change();
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
            $table->string('address_line_1')->nullable(false)->change();
            $table->string('county')->nullable(false)->change();
            $table->string('postcode')->nullable(false)->change();
        });
    }
}
