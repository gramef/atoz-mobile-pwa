<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestJobReminderController extends Controller
{
    public function run()
    {
        Artisan::call('send-interpreterjob-reminders');
        return 'Job reminders sent!';
    }
}
