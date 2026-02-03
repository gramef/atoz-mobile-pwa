<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('county');
            $table->string('postcode');
            $table->string('contact_number');
            $table->boolean('gender');
            $table->boolean('can_provide_affidavit')->nullable();
            $table->boolean('can_provide_affirmation')->nullable();
            $table->string('profile_picture');
            $table->string('dbs_number')->nullable();
            $table->date('dbs_expiry_date')->nullable();
            $table->date('induction_date')->nullable();
            $table->boolean('restrict_job_notifications')->default(0);
            $table->text('skype_details')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
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
        Schema::dropIfExists('agents');
    }
}
