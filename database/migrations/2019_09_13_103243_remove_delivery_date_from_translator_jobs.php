<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveDeliveryDateFromTranslatorJobs extends Migration
{
    public function up()
    {
        Schema::table('translator_jobs', function (Blueprint $table) {
            $table->dropColumn('delivery_date');
        });
    }

    public function down()
    {
        Schema::table('translator_jobs', function (Blueprint $table) {
            $table->date('delivery_date');
        });
    }
}
