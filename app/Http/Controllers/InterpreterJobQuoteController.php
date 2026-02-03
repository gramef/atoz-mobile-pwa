<?php

namespace App\Http\Controllers;

use App\InterpreterJob;
use Illuminate\Http\Request;

class InterpreterJobQuoteController extends Controller
{
    public function index(InterpreterJob $interpreterJob)
    {
        return view('interpreter-jobs.quotes.index', [
            'interpreterJob' => $interpreterJob,
            'quotes' => $interpreterJob->quotesVisibleToUser(auth()->user())->sortByDesc('created_at'),
        ]);
    }

    public function store(Request $request, InterpreterJob $interpreterJob)
    {
        $validated = $request->validate([
            'interpreting_cost' => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'travel_time' => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'travel_cost' => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'mileage_miles' => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'mileage_cost' => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'cost_description' => ['required', 'string'],
            'cost' => ['required', 'numeric', 'min:0', 'max:9999.99'],
        ]);

        if (auth()->user()->hasRole('admin')) {

            tap($interpreterJob)
                ->update([ 'status' => 5 ])
                ->adminQuotes()
                ->create($validated);

            return back()->with('success', 'Quote sent');
        }

        tap($interpreterJob->matchedLoggedInAgent())
            ->update([ 'status' => 2 ])
            ->quotes()
            ->create($validated);

        return back()->with('success', 'Quote sent');
    }
}

