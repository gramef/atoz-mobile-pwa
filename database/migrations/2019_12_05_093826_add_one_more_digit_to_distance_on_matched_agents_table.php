<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOneMoreDigitToDistanceOnMatchedAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('matched_agents', function (Blueprint $table) {
            $table->decimal('distance', 5, 1)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('matched_agents', function (Blueprint $table) {
            $table->decimal('distance', 4, 1)->change();
        });
    }
}
