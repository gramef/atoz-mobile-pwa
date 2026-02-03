<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableCompaniesAddAddressAndContactDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('vat_number')->nullable()->after('name');
            $table->string('company_number')->nullable()->after('vat_number');
            $table->string('address_line_1')->nullable()->after('company_number');
            $table->string('address_line_2')->nullable()->after('address_line_1');
            $table->string('county')->nullable()->after('address_line_2');
            $table->string('postcode')->nullable()->after('county');
            $table->string('email')->nullable()->after('postcode');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('vat_number');
            $table->dropColumn('company_number');
            $table->dropColumn('address_line_1');
            $table->dropColumn('address_line_2');
            $table->dropColumn('county');
            $table->dropColumn('postcode');
            $table->dropColumn('email');
        });
    }
}
