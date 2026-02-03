<?php

namespace App\Http\Controllers;

use App\Mail\TranslatorJobCommented;
use App\TranslatorJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TranslatorJobCommentController extends Controller
{
    public function store(Request $request, TranslatorJob $translatorJob)
    {
        $request->validate([
            'body' => ['required', 'string', 'max:255'],
        ]);

        $translatorJob->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $request->body,
        ]);

        $user = $request->user();
    
        foreach([config('app.to.address'), $translatorJob->client->user, $translatorJob->agent->user] as $adressees){
            Mail::to($adressees)->send(new TranslatorJobCommented(
                $translatorJob,
                'emails.translator-jobs.commented',
                "A new comment has been made by $user->first_name $user->last_name on the job $translatorJob->reference"
            ));
        }

        return back();
    }
}
