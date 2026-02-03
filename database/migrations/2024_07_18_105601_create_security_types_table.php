<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSecurityTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('security_types', function (Blueprint $table) {
            $table->increments('id'); // Primary key with auto-increment
            $table->string('name', 50);
            $table->boolean('deleted')->default(0);
            $table->string('details', 40)->nullable();
            //$table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            //$table->timestamp('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
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
        Schema::dropIfExists('security_types');
    }
}
