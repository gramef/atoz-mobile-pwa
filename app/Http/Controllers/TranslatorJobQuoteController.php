<?php

namespace App\Http\Controllers;

use App\Mail\QuoteMail;
use App\TranslatorJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TranslatorJobQuoteController extends Controller
{
    public function index(TranslatorJob $translatorJob)
    {
        return view('translator-jobs.quotes.index', [
            'translatorJob' => $translatorJob,
            'quotes' => $translatorJob->quotesVisibleToUser(auth()->user())->sortByDesc('created_at'),
        ]);
    }

    public function store(Request $request, TranslatorJob $translatorJob)
    {
        $validated = $request->validate([
            'cost_description' => ['required', 'string'],
            'cost' => ['required', 'numeric', 'min:0', 'max:9999.99'],
        ]);

        if (auth()->user()->hasRole('admin')) {

            tap($translatorJob)
                ->update([ 'status' => 5 ])
                ->adminQuotes()
                ->create($validated);

            return back()->with('success', 'Quote sent');
        }

        tap($translatorJob->matchedLoggedInAgent())
            ->update([ 'status' => 2 ])
            ->quotes()
            ->create($validated);

            Mail::to(config('app.to.address'))->send(new QuoteMail(
                $translatorJob,
                $translatorJob->matchedLoggedInAgent()->quotes->last(),
                'emails.translator-jobs.quote',
                'Job Quoted',
                'admin'
            ));
        return back()->with('success', 'Quote sent');
    }
}
