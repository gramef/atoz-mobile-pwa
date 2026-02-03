<?php

namespace App\Http\Controllers;

use App\InterpreterJob;
use App\Timesheet;
use App\Mail\JobMail;
use Illuminate\Support\Facades\Mail;

class CompletedInterpreterJobController extends Controller
{
    public function update(InterpreterJob $interpreterJob)
    {

        $timesheet = Timesheet::where('job_id', $interpreterJob->id)->first();
        if (empty($timesheet)) {
            $timesheet = new Timesheet();
        }
        $timesheet->agent_id = $interpreterJob->agent_id;
        $timesheet->job_id = $interpreterJob->id;
        $timesheet->status = 'Y';
        $timesheet->save();

        Mail::to($interpreterJob->agent->user)->send(new JobMail(
            $interpreterJob,
            'emails.agents.job-timesheet-sign',
            'Timesheet needs to be signed',
            'agent'
        ));

        $interpreterJob->update(['status' => 4]);
        return back()->with('success', 'Completed job');
    }
}