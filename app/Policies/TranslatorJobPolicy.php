<?php

namespace App\Policies;

use App\User;
use App\TranslatorJob;
use Illuminate\Auth\Access\HandlesAuthorization;

class TranslatorJobPolicy
{
    use HandlesAuthorization;

    public function view(User $user, TranslatorJob $translatorJob)
    {
        if (!$user->hasRole('agent')) {
            return false;
        }

        if (in_array(optional($translatorJob->matchedLoggedInAgent())->status, ['rejected', 'cancelled'])) {
            return false;
        }

        if ($translatorJob->statusName == 'cancelled') {
            return false;
        }

        if ($user->agent->is($translatorJob->agent)) {
            return true;
        }

        if ($translatorJob->agent()->exists()) {
            return false;
        }

        if ($user->agent->isMatchedToJob($translatorJob)) {
            return true;
        }

        return false;
    }

    public function edit(User $user, TranslatorJob $translatorJob)
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($translatorJob->client->is($user->client)) {
            return true;
        }

        return false;
    }

    public function update(User $user, TranslatorJob $translatorJob)
    {
        if ($user->cannot('edit', $translatorJob)) {
            return false;
        }
        /*
                if ($translatorJob->cannotBeCancelled()) {
                    return false;
                }

                        if ($translatorJob->target_date->isFuture()) {
                            return true;
                        }

                        return false;
                        */
        return true;
    }


    public function viewQuotes(User $user, TranslatorJob $translatorJob)
    {
        if ($user->can('view', $translatorJob)) {
            return true;
        }

        if ($user->can('edit', $translatorJob)) {
            return true;
        }

        return false;
    }

    public function accept(User $user, TranslatorJob $translatorJob)
    {
        if (!$translatorJob->isActive()) {
            return false;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->can('view', $translatorJob)) {
            return true;
        }

        return false;
    }

    public function assign(User $user, TranslatorJob $translatorJob)
    {
        if (!$user->hasRole('admin')) {
            return false;
        }

        if (!$translatorJob->isActive()) {
            return false;
        }

        if ($translatorJob->adminQuotes->isNotEmpty()) {
            return true;
        }

        return false;
    }

    public function viewDocuments(User $user, TranslatorJob $translatorJob)
    {
        if ($user->hasRole(['admin', 'super admin'])) {
            return true;
        }
        if (!$translatorJob->agent) {
            return false;
        }

        if ($user->can('edit', $translatorJob)) {
            return true;
        }

        if (optional($user->agent)->is($translatorJob->agent)) {
            return true;
        }

        return false;
    }

    public function complete(User $user, TranslatorJob $translatorJob)
    {
        if ($user->cannot('edit', $translatorJob)) {
            return false;
        }

        if ($translatorJob->canBeCompleted()) {
            return true;
        }

        return false;
    }
}
