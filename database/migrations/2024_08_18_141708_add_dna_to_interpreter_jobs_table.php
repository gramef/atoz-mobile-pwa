<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDnaToInterpreterJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

  public function up()
{
    Schema::table('interpreter_jobs', function (Blueprint $table) {
        $table->boolean('dna')->default(false)->after('status'); // 'some_column' is the column after which this new column will be placed
    });
}

public function down()
{
    Schema::table('interpreter_jobs', function (Blueprint $table) {
        $table->dropColumn('dna');
    });
}
}
