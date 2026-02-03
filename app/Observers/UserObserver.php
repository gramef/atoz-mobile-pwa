<?php

namespace App\Observers;

use App\Mail\UserCreatedMail;
use App\Mail\UserMail;
use App\User;
use Illuminate\Support\Facades\Mail;

class UserObserver
{
    public function creating(User $user)
    {
        $user->email_verified_at = now();
    }

    public function updating(User $user)
    {
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
            $user->sendEmailVerificationNotification();
        }

        if ($user->isDirty('enabled') && $user->enabled && $user->hasRole('new-client')) {
            Mail::to($user)->send(new UserCreatedMail(
                app('auth.password.broker')->createToken($user),
                $user,
                'emails.clients.approved',
                'Request approved'
            ));
        }
    }
}
