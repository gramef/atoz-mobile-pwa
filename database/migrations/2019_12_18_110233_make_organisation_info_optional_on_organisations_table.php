<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeOrganisationInfoOptionalOnOrganisationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organisations', function (Blueprint $table) {
            $table->string('organisation_address_line_1')->nullable()->change();
            $table->string('organisation_county')->nullable()->change();
            $table->string('organisation_postcode')->nullable()->change();
            $table->string('organisation_email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organisations', function (Blueprint $table) {
            $table->string('organisation_address_line_1')->nullable(false)->change();
            $table->string('organisation_county')->nullable(false)->change();
            $table->string('organisation_postcode')->nullable(false)->change();
            $table->string('organisation_email')->nullable(false)->change();
        });
    }
}
