<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOneMoreDigitToColumnsOnQuoteTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->decimal('cost', 6, 2)->change();
            $table->decimal('mileage', 6, 2)->change();
            $table->decimal('expenses', 6, 2)->change();
        });

        Schema::table('admin_quotes', function (Blueprint $table) {
            $table->decimal('cost', 6, 2)->change();
            $table->decimal('mileage', 6, 2)->change();
            $table->decimal('expenses', 6, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->decimal('cost', 5, 2)->change();
            $table->decimal('mileage', 5, 2)->change();
            $table->decimal('expenses', 5, 2)->change();
        });

        Schema::table('admin_quotes', function (Blueprint $table) {
            $table->decimal('cost', 5, 2)->change();
            $table->decimal('mileage', 5, 2)->change();
            $table->decimal('expenses', 5, 2)->change();
        });
    }
}
