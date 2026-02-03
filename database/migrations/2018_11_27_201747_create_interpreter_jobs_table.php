<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInterpreterJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interpreter_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id');
            $table->integer('service_type');
            $table->boolean('require_qualified');
            $table->integer('from_language_id');
            $table->integer('to_language_id');
            $table->integer('status')->default(0);
            $table->integer('gender');
            $table->text('special_requirements')->nullable();
            $table->text('contact_information');
            $table->string('title')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('company')->nullable();
            $table->string('personal_identity_number')->nullable();
            $table->string('user_title')->nullable();
            $table->string('user_first_name')->nullable();
            $table->string('user_last_name')->nullable();
            $table->string('user_email')->nullable();
            $table->string('user_contact_number')->nullable();
            $table->string('user_company')->nullable();
            $table->string('client_reference');
            $table->date('appointment_date');
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('county');
            $table->string('postcode');
            $table->string('start_time');
            $table->string('end_time');
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
        Schema::dropIfExists('interpreter_jobs');
    }
}
