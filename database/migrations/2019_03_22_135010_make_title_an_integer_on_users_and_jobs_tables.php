<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeTitleAnIntegerOnUsersAndJobsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('title')->change();
        });

        Schema::table('interpreter_jobs', function (Blueprint $table) {
            $table->integer('title')->change();
            $table->integer('user_title')->change();
        });

        Schema::table('translator_jobs', function (Blueprint $table) {
            $table->integer('title')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('title')->change();
        });

        Schema::table('interpreter_jobs', function (Blueprint $table) {
            $table->string('title')->change();
            $table->string('user_title')->change();
        });

        Schema::table('translator_jobs', function (Blueprint $table) {
            $table->string('title')->change();
        });
    }
}
