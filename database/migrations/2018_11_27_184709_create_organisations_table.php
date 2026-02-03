<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrganisationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organisations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id');
            $table->string('organisation_company');
            $table->string('vat_number');
            $table->string('company_number');
            $table->string('organisation_address_line_1');
            $table->string('organisation_address_line_2')->nullable();
            $table->string('organisation_county');
            $table->string('organisation_postcode');
            $table->string('organisation_email');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organisations');
    }
}
