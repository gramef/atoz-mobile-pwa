<?php

namespace App\Jobs;

use App\Agent;
use App\MatchedAgent;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class MatchAgents implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $newJob;
    protected $matchedAgentIdToIgnore;
    protected $matchedAgentIdToSelect;

    public function __construct( $newJob, int $matchedAgentIdToIgnore = null, int $matchedAgentIdToSelect = null )
    {
        $this->newJob 					= $newJob;
        $this->matchedAgentIdToIgnore 	= $matchedAgentIdToIgnore;
        $this->matchedAgentIdToSelect 	= $matchedAgentIdToSelect;
    }

    public function handle()
    {
        if ( get_class($this->newJob) == 'App\InterpreterJob') {

            $matchedAgents = Agent::when( $this->matchedAgentIdToSelect === null, function ($query) {
                //	return $query->canBeMatchedToJobs($this->newJob)
                #	return $query->canBeMatchedToInterpreterJobs( $this->newJob )
                #	    ->whereDoesntHave( 'matchedJobs', function ($q) {
                #	        $q->where( 'id', $this->matchedAgentIdToIgnore );
                #	        //  $q->orWhere('status', 1);
                #	    })
                #	    ->hasEnabledUser()
                #	    ->matchesInterpreterJob( $this->newJob );
					
				return $query->matchesInterpreterJob( $this->newJob )
                    ->whereDoesntHave( 'matchedJobs', function ($q) {
                        $q->where( 'id', $this->matchedAgentIdToIgnore );
                        //  $q->orWhere('status', 1);
                    })
                    ->hasEnabledUser();
            }, function ($query) {
                return $query->where( 'id', $this->matchedAgentIdToSelect );
            })
				->withDistanceFromInterpreterJob( $this->newJob )
                ->get()
                ->map( function ( $agent ) {
                    return [
                        'agent_id' => $agent->id,
                        'distance' => $agent->distance,
                    ];
                });
				
				
			#################### DON'T TOUCH #######################
				
            // $previouslyRejectedAgents = MatchedAgent::where('job_id', $this->newJob->id)
            // ->where('status', 3) // Status 1 indicates rejected
            // ->get();

            // foreach ($previouslyRejectedAgents as $rejectedAgent) {
            //     $matchedAgents->push([
            //         'agent_id' => $rejectedAgent->agent_id,
            //         'distance' => $rejectedAgent->distance,
            //     ]);
            // }

        } elseif (get_class( $this->newJob) == 'App\TranslatorJob' ) {

            $matchedAgents = Agent::when($this->matchedAgentIdToSelect === null, function ($query) {
                //return $query->canBeMatchedToJobs()
                return $query->canBeMatchedToTranslatorJobs()
                    ->whereDoesntHave('matchedJobs', function ($q) {
                        $q->where('id', $this->matchedAgentIdToIgnore);
                        $q->orWhere('status', 1);
                    })
                    ->hasEnabledUser()
                    ->matchesTranslatorJob($this->newJob);
            }, function ($query) {
                return $query->where('id', $this->matchedAgentIdToSelect);
            })
                ->get()
                ->map(function ($agent) {
                    return [
                        'agent_id' => $agent->id,
                    ];
                });
        }

		$this->newJob->matchedAgents()->where( 'id', '!=', $this->matchedAgentIdToIgnore )->delete();
        $this->newJob->matchedAgents()->createMany( $matchedAgents->all() );
		
    }
}
