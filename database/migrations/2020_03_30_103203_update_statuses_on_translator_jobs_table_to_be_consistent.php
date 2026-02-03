<?php

use App\TranslatorJob;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateStatusesOnTranslatorJobsTableToBeConsistent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // TranslatorJob::flushEventListeners();

        TranslatorJob::where('status', 1)->update(['status' => 5]);

        TranslatorJob::where('status', 3)->update(['status' => 1]);

        TranslatorJob::where('status', 5)->update(['status' => 3]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        TranslatorJob::where('status', 5)->update(['status' => 1]);

        TranslatorJob::where('status', 1)->update(['status' => 3]);

        TranslatorJob::where('status', 3)->update(['status' => 5]);
    }
}

