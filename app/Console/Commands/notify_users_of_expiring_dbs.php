<?php

namespace App\Console\Commands;

use App\Mail\DBSMail;
use Illuminate\Console\Command;
use App\Notifications\DBSExpiring;
use App\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class notify_users_of_expiring_dbs extends Command
{

    protected $signature = 'notify_users_of_expiring_dbs';

    protected $description = 'Send an email to users letting them know their DBS number is expiring';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $users = User::hasExpiredDBS()->get();

        if (!$users->isEmpty()) {

            foreach ($users as $user) {
                Log::info("Emailing user with id {$user->id} about their DBS expiring");

                $user->notify(new DBSExpiring);

                $user->agent->update(['notified_of_dbs' => true]);

                Mail::to(config('app.to.address'))->send(new DBSMail(
                    $user->agent,
                    'emails.agents.dbs-expiring',
                    'Agent DBS Expiring'
                ));

            }
        }

        $users = User::hasExpiredDBS2()->get();

        if (!$users->isEmpty()) {

            foreach ($users as $user) {
                Log::info("Emailing user with id {$user->id} about their DBS expiring");

                $user->notify(new DBSExpiring);

                $user->agent->update(['notified_week_of_dbs' => true]);

                Mail::to(config('app.to.address'))->send(new DBSMail(
                    $user->agent,
                    'emails.agents.dbs-expiring',
                    'Agent DBS Expiring'
                ));
            }
        }

    }
}
