<?php

namespace App\Http\Controllers;
use App\User;
use App\Mail\UserCreatedMail;
use Illuminate\Support\Facades\Mail;

class ResetUserPassword extends Controller
{
    public function __invoke(User $user)
    {
        
        Mail::to($user)->send(new UserCreatedMail(
            app('auth.password.broker')->createToken($user), 
            $user, 
            'emails.users.reset-password', 
            'Reset account password'
        ));

        return back()->with('success', 'Sent reset password link');
    }
}
