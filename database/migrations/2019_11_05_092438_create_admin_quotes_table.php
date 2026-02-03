<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_quotes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('job_type');
            $table->integer('job_id');
            $table->decimal('cost', 5, 2);
            $table->text('cost_description');
            $table->decimal('mileage', 5, 2)->nullable();
            $table->text('mileage_description')->nullable();
            $table->decimal('expenses', 5, 2)->nullable();
            $table->text('expenses_description')->nullable();
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('admin_quotes');
    }
}
