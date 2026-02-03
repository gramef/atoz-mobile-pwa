<?php

use App\Agent;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDefaultValueOfAffidavitAndAffirmationToFalseOnAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Agent::where('can_provide_affidavit', null)
            ->orWhere('can_provide_affirmation', null)
            ->update([
                'can_provide_affidavit' => false,
                'can_provide_affirmation' => false,
            ]);

        Schema::table('agents', function (Blueprint $table) {
            $table->boolean('can_provide_affidavit')->default(false)->change();
            $table->boolean('can_provide_affirmation')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Agent::where('can_provide_affidavit', false)
            ->orWhere('can_provide_affirmation', false)
            ->update([
                'can_provide_affidavit' => null,
                'can_provide_affirmation' => null,
            ]);

        Schema::table('agents', function (Blueprint $table) {
            $table->boolean('can_provide_affidavit')->default(null)->change();
            $table->boolean('can_provide_affirmation')->default(null)->change();
        });
    }
}
