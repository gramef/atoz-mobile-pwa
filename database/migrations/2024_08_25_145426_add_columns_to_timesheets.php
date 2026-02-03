<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToTimesheets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('timesheets', function (Blueprint $table) {
            $table->integer('agent_start_time')->nullable()->default(0);
            $table->integer('agent_end_time')->nullable()->default(0);
            $table->integer('client_phone')->nullable();
            $table->string('client_name')->nullable();
            $table->string('client_designation')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('timesheets', function (Blueprint $table) {
            $table->dropColumn(['agent_start_time', 'agent_end_time', 'client_phone','client_name','client_designation']);
        });
    }
}
