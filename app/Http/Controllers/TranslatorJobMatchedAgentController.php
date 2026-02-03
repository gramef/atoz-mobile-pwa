<?php

namespace App\Http\Controllers;

use App\Agent;
use App\Mail\JobMail;
use App\MatchedAgent;
use App\TranslatorJob;
use App\Jobs\MatchAgents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TranslatorJobMatchedAgentController extends Controller
{
    public function index(TranslatorJob $translatorJob)
    {
        return view( 'translator-jobs.matched.index', [
            'translatorJob' => $translatorJob,
            'matchedAgents' => $translatorJob->matchedAgents()
                ->with([
                    'agent.user',
                    'quotes',
                    'job.adminQuotes',
                ])->get(),
            'unmatchedAgents' => Agent::with('user')
				->canBeMatchedToTranslatorJobs()
                ->hasEnabledUser()
                //	->matchesTranslatorJob( $translatorJob )
                ->whereNotIn( 'id', $translatorJob->matchedAgents->pluck( 'agent_id' )->all() )
                ->get()
                ->mapWithKeys(function ($agent) {
                    return [
                        $agent->id => $agent->user->getFullName(),
                    ];
                })
        ]);
    }

    public function store(Request $request, TranslatorJob $translatorJob)
    {
        $translatorJob->matchedAgents()->create([
            'agent_id' => $request->agent_id,
        ]);

        return redirect()->back()->with('success', 'Agent notified');
    }

    public function update(TranslatorJob $translatorJob, MatchedAgent $matchedAgent = null)
    {
        if (auth()->user()->hasRole('admin')) {

            $matchedAgent->update([ 'status' => 4 ]);

            optional($matchedAgent->latestQuote())->update([ 'status' => 1 ]);

            $translatorJob->update([
                'agent_id' => $matchedAgent->agent->id,
                'status' => 1,
            ]);

            return redirect()->back()->with('success', 'Agent assigned');
        }

        tap($translatorJob)
            ->update([
                'agent_id' => auth()->user()->agent->id,
                'status' => 1,
            ])
            ->matchedLoggedInAgent()
            ->update([ 'status' => 4 ]);

        return redirect()->back()->with('success', 'Job accepted');
    }

    public function destroy(TranslatorJob $translatorJob, MatchedAgent $matchedAgent)
    {
        $agentUser = $translatorJob->agent->user;

        $translatorJob->update([
            'agent_id' => null,
            'status' => 0
        ]);

        if ($matchedAgent->status_id == 4) {
            MatchAgents::dispatch($translatorJob, $matchedAgent->id);

            Mail::to($translatorJob->client->user)->send(new JobMail(
                $translatorJob,
                'emails.matched-agents.agent-unavailable',
                'Agent no longer available',
                'client'
            ));

            Mail::to($agentUser)->send(new JobMail(
                $translatorJob,
                'emails.agents.unassigned',
                'Unassigned from job',
                'agent'
            ));
        }

        $matchedAgent->update([ 'status' => 3 ]);

        return redirect()->back()->with('success', 'Agent unassigned');
    }

    public function reject(TranslatorJob $translatorJob)
    {
        /** agent rejected client's changes */
        if ($translatorJob->agent) {

            $translatorJob->update([
                'agent_id' => null,
                'status' => 0
            ]);

            Mail::to(config('app.to.address'))->send(new JobMail(
                $translatorJob,
                'emails.matched-agents.rejected-changes',
                'Agent rejected client\'s changes',
                'admin'
            ));

            Mail::to($translatorJob->client->user)->send(new JobMail(
                $translatorJob,
                'emails.matched-agents.agent-unavailable',
                'Agent no longer available',
                'client'
            ));

            MatchAgents::dispatch($translatorJob);
        }

        $translatorJob
            ->matchedLoggedInAgent()
            ->update([
                'status' => 1
            ]);

        return redirect()->route('translator-jobs.index')->with('success', 'Job rejected');
    }
}
