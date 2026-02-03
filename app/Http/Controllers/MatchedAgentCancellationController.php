<?php

namespace App\Http\Controllers;

use App\Mail\JobMail;
use App\MatchedAgent;
use App\Jobs\MatchAgents;
use App\Http\Requests\CancelRequest;
use Illuminate\Support\Facades\Mail;
use App\Traits\LogsUpdates;

class MatchedAgentCancellationController extends Controller
{
    use LogsUpdates;
    public function store(CancelRequest $request, MatchedAgent $matchedAgent)
    {
        $route = get_class($matchedAgent->job) === 'App\InterpreterJob' ? 'interpreter-jobs.index' : 'translator-jobs.index';
//print_r($matchedAgent->job->id);
//echo "<pre>";print_r($matchedAgent->job);echo "</pre>";exit;
        tap($matchedAgent)
            ->update(['status' => 3])
            ->cancellation()
            ->create(['message' => $request->message]);

        if (!$matchedAgent->job->agent) {
            return redirect()->route($route)->with('success', 'Job Cancelled');
        }

        // Store the agent_id before nullifying it
        $agentId = $matchedAgent->job->agent_id;

        $matchedAgent->job->update([
            'agent_id' => null,
            'status' => 0,
        ]);

        Mail::to(config('app.to.address'))->send(new JobMail(
            $matchedAgent->job,
            'emails.matched-agents.cancelled',
            'Agent has cancelled their assigned job',
            'admin'
        ));

        Mail::to($matchedAgent->job->client->user)->send(new JobMail(
            $matchedAgent->job,
            'emails.matched-agents.agent-unavailable',
            'Agent no longer available',
            'client'
        ));
       $this->logUpdate(
            $matchedAgent->job->id,
            'interpreter-jobs',
            auth()->user()->id,
            $agentId,
           1,
            'Job Rejected by Agent'
        );
        MatchAgents::dispatch($matchedAgent->job, $matchedAgent->id);

        return redirect()->route($route)->with('success', 'Job Cancelled');
    }
}

