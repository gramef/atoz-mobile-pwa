<?php

namespace App\Http\Controllers;

use App\Agent;
use App\Mail\JobMail;
use App\MatchedAgent;
use App\InterpreterJob;
use App\Jobs\MatchAgents;
use App\Traits\LogsUpdates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Timesheet;

class InterpreterJobMatchedAgentController extends Controller
{
    use LogsUpdates;
    public function index(InterpreterJob $interpreterJob)
    {
				
		$matchedAgentsOptions = Agent::matchesInterpreterJob($interpreterJob)
		->with(['user' => function ($query) {
			return $query->select( 'id', 'first_name', 'last_name' );
		}])
		->select( 'id', 'user_id' )
		->get()
		->mapWithKeys(function ($query) {			
			return [
				$query->user->id => $query->user->getFullName(),
			];
		});	
		
		#	dd( $matchedAgentsOptions );	
		
		      
        // dd($interpreterJob->security_type_id);
        return view('interpreter-jobs.matched.index', [

            'interpreterJob' => $interpreterJob,
            'matchedAgents' => $interpreterJob->matchedAgents()
                ->with([
                    'agent.user',
                    'quotes',
                    'job.adminQuotes',
                ])
                ->get(),
			'matchedAgentsOptions' => $matchedAgentsOptions,				
            'unmatchedAgents' => Agent::with('user')
                // ->canBeMatchedToJobs()
                ->canBeMatchedToInterpreterJobs($interpreterJob)
                ->hasEnabledUser()
                ->matchesInterpreterJob($interpreterJob)
                ->whereNotIn('id', $interpreterJob->matchedAgents->pluck('agent_id')->all())
                ->get()
                ->mapWithKeys(function ($agent) {
                    return [
                        $agent->id => $agent->user->getFullName(),
                    ];
                })
        ]);
    }

    public function store(Request $request, InterpreterJob $interpreterJob)
    {
        $interpreterJob->matchedAgents()->create([
            'agent_id' => $request->agent_id,
            'distance' => Agent::where('id', $request->agent_id)
                ->withDistanceFromInterpreterJob($interpreterJob)
                ->first()
                ->distance
        ]);
        return back()->with('success', 'Agent notified');
    }

    public function update(InterpreterJob $interpreterJob, MatchedAgent $matchedAgent = null)
    {
        if (auth()->user()->hasRole('admin')) {
            if ($interpreterJob->isVisible($matchedAgent->agent->user)) {

                // $timesheet = new Timesheet();
                // $timesheet->agent_id = $matchedAgent->agent->id;
                // $timesheet->job_id = $interpreterJob->id;
                // $timesheet->status = 'Y';
                // $timesheet->save();
                $matchedAgent->update(['status' => 4]);

                optional($matchedAgent->latestQuote())->update(['status' => 1]);

                $interpreterJob->update([
                    'agent_id' => $matchedAgent->agent->id,
                    'status' => 1,
                ]);

                $this->logUpdate(
                    $interpreterJob->id,
                    'interpreter-jobs',
                    auth()->user()->id,
                    $interpreterJob->agent_id,
                    '4',
                    'Interpreter is assigned to this job'
                );
                return back()->with('success', 'Agent assigned');
            } else {
                return back()->with('failure', 'That agent cannot be assigned to that job. They may be double booked, or they have rejected the job.');
            }
        }

        if ($interpreterJob->isVisible(auth()->user())) {
            tap($interpreterJob)
                ->update([
                    'agent_id' => auth()->user()->agent->id,
                    'status' => 1,
                ])
                ->matchedLoggedInAgent()
                ->update(['status' => 4]);

            // $timesheet = new Timesheet();
            // $timesheet->agent_id = auth()->user()->agent->id;
            // $timesheet->job_id = $interpreterJob->id;
            // $timesheet->status = 'Y';
            // $timesheet->save();

            //Log update start agent assign
            $this->logUpdate(
                $interpreterJob->id,
                'interpreter-jobs',
                auth()->user()->id,
                $interpreterJob->agent_id,
                '4',
                'Agent is assigned to this job'
            );
            //Log update and agent assign


            return back()->with('success', 'Job accepted');
        } else {
            return back()->with('failure', 'You cannot accept this job');

        }
    }

    public function destroy(InterpreterJob $interpreterJob, MatchedAgent $matchedAgent)
    {

        $agentUser = $interpreterJob->agent->user;

        $interpreterJob->update([
            'agent_id' => null,
            'status' => 0
        ]);

        if ($matchedAgent->status_id == 4) {
            MatchAgents::dispatch($interpreterJob, $matchedAgent->id);

            Mail::to($interpreterJob->client->user)->send(new JobMail(
                $interpreterJob,
                'emails.matched-agents.agent-unavailable',
                'Agent no longer available',
                'client'
            ));
            //Log update start agent assign
            $this->logUpdate(
                $interpreterJob->id,
                'interpreter-jobs',
                auth()->user()->id,
                $interpreterJob->agent_id,
                '0',
                'Agent Unassigned from the job'
            );
            //Log update end agent assign

            Mail::to($agentUser)->send(new JobMail(
                $interpreterJob,
                'emails.agents.unassigned',
                'Unassigned from job',
                'agent'
            ));
        }

        $matchedAgent->update([ 'status' => 3 ]);

        return back()->with('success', 'Agent unassigned');
    }
    public function unAssignForAgents(InterpreterJob $interpreterJob, MatchedAgent $matchedAgent)
    {
        // dd(auth()->user()->roles);
        $agentUser = $interpreterJob->agent->user;

        $interpreterJob->update([
            'agent_id' => null,
            'status' => 0
        ]);

        if ($matchedAgent->status_id == 4) {
            MatchAgents::dispatch($interpreterJob, $matchedAgent->id);

            Mail::to($interpreterJob->client->user)->send(new JobMail(
                $interpreterJob,
                'emails.matched-agents.agent-unavailable',
                'Agent no longer available',
                'client'
            ));
            //Log update start agent assign
            $this->logUpdate(
                $interpreterJob->id,
                'interpreter-jobs',
                auth()->user()->id,
                $interpreterJob->agent_id,
                '0',
                'Agent Unassigned from the job'
            );
            //Log update end agent assign

            Mail::to($agentUser)->send(new JobMail(
                $interpreterJob,
                'emails.agents.unassigned',
                'Unassigned from job',
                'agent'
            ));
        }

        $matchedAgent->update([ 'status' => 3 ]);

        return back()->with('success', 'Agent unassigned');
    }

    public function reject(InterpreterJob $interpreterJob)
    {
        /** agent rejected client's changes */
        if ($interpreterJob->agent) {

            $interpreterJob->update([
                'agent_id' => null,
                'status' => 0
            ]);

            Mail::to(config('app.to.address'))->send(new JobMail(
                $interpreterJob,
                'emails.matched-agents.rejected-changes',
                'Agent rejected client\'s changes',
                'admin'
            ));

            //Log update start agent assign
            $this->logUpdate(
                $interpreterJob->id,
                'interpreter-jobs',
                auth()->user()->id,
                $interpreterJob->agent_id,
                '0',
                'Agent Rejected this job'
            );
            //Log update end agent assign

            Mail::to($interpreterJob->client->user)->send(new JobMail(
                $interpreterJob,
                'emails.matched-agents.agent-unavailable',
                'Agent no longer available',
                'client'
            ));

            MatchAgents::dispatch($interpreterJob);
        }

        $interpreterJob
            ->matchedLoggedInAgent()
            ->update([
                'status' => 1
            ]);

        return redirect()->route('interpreter-jobs.index')->with('success', 'Job rejected');
    }
}
