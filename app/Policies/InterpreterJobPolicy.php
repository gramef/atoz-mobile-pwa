<?php

namespace App\Policies;

use App\User;
use App\InterpreterJob;
use Illuminate\Auth\Access\HandlesAuthorization;

class InterpreterJobPolicy
{
    use HandlesAuthorization;

    public function view(User $user, InterpreterJob $interpreterJob)
    {
     
        if (!$user->hasRole('agent')) {
            return false;
        }
         if (in_array(optional($interpreterJob->matchedLoggedInAgent())->status, ['rejected'])) {
            return true;
        }

        if (in_array(optional($interpreterJob->matchedLoggedInAgent())->status, ['cancelled'])) {
            return false;
        }


        if ($interpreterJob->statusName == 'cancelled') {
            return false;
        }

        if ($user->agent->is($interpreterJob->agent)) {
            return true;
        }

        if ($interpreterJob->agent) {
            $this->deny('This assignment is no longer available!');
        }

        if ($user->agent->isMatchedToJob($interpreterJob)) {
            return true;
        }

        return false;
    }
    public function dna(InterpreterJob $interpreterJob)
    {
        if($user->hasRole('admin'))
        {
            return true;
        }
        if($interpreterJob->canBeDna){
            return true;
        }
    }
     public function retrn(InterpreterJob $interpreterJob)
    {
        if($user->hasRole('admin'))
        {
            return true;
        }
        if($interpreterJob->canBeRetrn){
            return true;
        }
    }
    public function edit(User $user, InterpreterJob $interpreterJob)
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($interpreterJob->client->is($user->client)) {
            return true;
        }

        return false;
    }

    public function update(User $user, InterpreterJob $interpreterJob)
    {
        if ($user->cannot('edit', $interpreterJob)) {
            return false;
        }

        // if ($interpreterJob->cannotBeCancelled()) {
        //     return false;
        // }

        $start = $interpreterJob->appointment_date->setTimeFromTimeString($interpreterJob->start_time);

        if (true) {
            return true;
        }

        return true;
    }

    public function viewQuotes(User $user, InterpreterJob $interpreterJob)
    {
        if (!$interpreterJob->requiresQuote()) {
            return false;
        }

        if ($user->can('view', $interpreterJob)) {
            return true;
        }

        if ($user->can('edit', $interpreterJob)) {
            return true;
        }

        return false;
    }

    public function accept(User $user, InterpreterJob $interpreterJob)
    {
        if (!$interpreterJob->isActive()) {
            return false;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->can('view', $interpreterJob)) {
            return true;
        }

        return false;
    }

    public function assign(User $user, InterpreterJob $interpreterJob)
    {
        if (!$user->hasRole('admin')) {
            return false;
        }

        if (!$interpreterJob->isActive()) {
            return false;
        }

        if (!$interpreterJob->client->always_requires_a_quote) {
            return true;
        }

        if ($interpreterJob->adminQuotes->isNotEmpty()) {
            return true;
        }

        return false;
    }
        public function complete(User $user, InterpreterJob $interpreterJob)
    {
        // if ($user->cannot('edit', $interpreterJob)) {
        //     return false;
        // }

        if ($interpreterJob->canBeCompleted()) {
            return true;
        }

        return false;
    }

 
}
