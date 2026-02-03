<?php

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

class SetEmailVerifiedAtToCurrentDateOnUsersTable extends Migration
{
    public function up()
    {
        User::where('email_verified_at', '=', null)->update([ 'email_verified_at' => Carbon::now() ]);
    }

    public function down()
    {

    }
}
