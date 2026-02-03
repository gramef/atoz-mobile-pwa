<?php

use App\User;
use Illuminate\Database\Migrations\Migration;

class AddAdminRoleToSuperAdmin extends Migration
{
    public function up()
    {
        if (User::where('email', 'abida@atozinterpreting.com')->exists()) {
            User::where('email', 'abida@atozinterpreting.com')->first()->assignRole('admin');
        }
    }

    public function down()
    {
        if (User::where('email', 'abida@atozinterpreting.com')->exists()) {
            User::where('email', 'abida@atozinterpreting.com')->first()->removeRole('admin');
        }
    }
}
