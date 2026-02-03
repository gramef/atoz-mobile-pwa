<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTranslatorJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('translator_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id');
            $table->integer('from_language_id');
            $table->integer('to_language_id');
            $table->integer('status')->default(0);
            $table->integer('word_count');
            $table->text('notes')->nullable();
            $table->string('title')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('company')->nullable();
            $table->date('target_date');
            $table->string('client_reference');
            $table->string('file');
            $table->boolean('affirmation');
            $table->boolean('affidavit');
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
        Schema::dropIfExists('translator_jobs');
    }
}
