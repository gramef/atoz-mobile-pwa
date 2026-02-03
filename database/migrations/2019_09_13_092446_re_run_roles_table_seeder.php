<?php

use Illuminate\Database\Migrations\Migration;

class ReRunRolesTableSeeder extends Migration
{
    public function up()
    {
        $seeder = new RolesTableSeeder();
        $seeder->run();
    }

    public function down()
    {
        
    }
}
