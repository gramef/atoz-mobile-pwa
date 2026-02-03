<?php

namespace App\Observers;

use App\MatchedAgent;
use App\Jobs\SendEmail;
use App\Mail\MatchedAgentMail;

class MatchedAgentObserver
{
    public function created(MatchedAgent $matchedAgent)
    {
        SendEmail::dispatch($matchedAgent->agent->user, new MatchedAgentMail($matchedAgent));
    }
}