<?php

namespace App\Http\Controllers;

use App\AdminQuote;
use App\Mail\AcceptedQuoteMail;
use Illuminate\Support\Facades\Mail;

class AdminQuoteController extends Controller
{
    public function update(AdminQuote $adminQuote)
    {
        $template = 'emails.translator-jobs.accepted-quote';
        if ($adminQuote->job_type == 'App\InterpreterJob') {
            $template = 'emails.interpreter-jobs.accepted-quote';
        }
        $adminQuote->update([ 'status' => 1 ]);

        Mail::to(config('app.to.address'))->send(new AcceptedQuoteMail(
            $adminQuote,
            $template,
            'Job Quote',
            'admin'
        ));
        return redirect()->back()->with('success', 'Quote accepted');
    }

    public function destroy(AdminQuote $adminQuote)
    {
        $adminQuote->update([ 'status' => 2 ]);

        $adminQuote->job->update([ 'status' => 3 ]);

        $adminQuote->job->assignedMatched()->first()->update([ 'status' => 1 ]);

        return redirect()->back()->with('success', 'Quote rejected');
    }
}
