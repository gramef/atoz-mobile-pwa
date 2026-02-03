<?php

namespace App\Http\Controllers;

use App\TranslatorJob;
use App\Http\Requests\CancelRequest;

class TranslatorJobCancellationController extends Controller
{
    public function store(CancelRequest $request, TranslatorJob $translatorJob)
    {
        $translatorJob->cancel($request->message);

        return redirect()->route('translator-jobs.index')->with('success', 'Job Cancelled');
    }
}
