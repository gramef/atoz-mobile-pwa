<?php

namespace App\Policies;

use App\User;
use App\Document;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentPolicy
{
    use HandlesAuthorization;

    public function delete(User $user, Document $document)
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('client')) {
            return false;
        }

        if ($user->hasRole('agent')) {
            return false;
        }

        if ($user->agent->is($document->documentable)) {
            return true;
        }

        return false;
    }
}
