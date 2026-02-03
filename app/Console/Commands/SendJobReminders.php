<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\InterpreterJob;
use App\User;
use App\Notifications\JobReminder;
use Carbon\Carbon;
use Log;
class SendJobReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-interpreterjob-reminders';
    protected $description = 'Send job reminders to interpreters';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // $array_jobs=$jobs->toArray();
 $date = Carbon::now()->timezone('Europe/London')->addDay();
  Log::info("Running job reminder check for date: {$date->toDateString()}");

$interpreterJobs = InterpreterJob::hasEnabledUser()
        ->with([
            'toLanguage',
            'matchedAgents',
            'agent.user',])
            ->where('reminder_sent',false)
     ->whereDate('appointment_date',$date->toDateString())    
    ->whereHas('matchedAgents', function($query) {
        $query->where('status', 4);
    })
    ->get();

$usersWithJobs = $interpreterJobs->filter(function ($job) {
        return $job->isExactly24HoursRemaining();
    })->map(function ($job) {
        $user = $job->agent->user;
        $user->interpreter_job = $job; // Attach the interpreter job details to the user object
        return $user;
    });

if (!$usersWithJobs->isEmpty()) {
    foreach ($usersWithJobs as $user) {
      Log::info("Job reminder is sent to Agent {{$user->email}} for Job ID: {$user->interpreter_job->id}");
        Log::info("Job reminder is sent to Agent {{$user->email}}");
        $user->notify(new JobReminder);

        // Update the reminder_sent status after sending the notification
        $user->interpreter_job->reminder_sent = true;
        $user->interpreter_job->save();
    }
}else{
       Log::info("No jobs found to send reminders.");
    
}
}
    
}
