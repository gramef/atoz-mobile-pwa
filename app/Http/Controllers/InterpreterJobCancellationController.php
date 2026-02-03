<?php

namespace App\Http\Controllers;

use App\InterpreterJob;
use App\Http\Requests\CancelRequest;
use App\Traits\LogsUpdates;

class InterpreterJobCancellationController extends Controller
{
    use LogsUpdates;
    public function store(CancelRequest $request, InterpreterJob $interpreterJob)
    {
           $this->logUpdate(
            $interpreterJob->id,
            'interpreter-jobs',
            auth()->user()->id,
            $interpreterJob->agent_id,
            '3',
            'Job cancelled'
        );
        $interpreterJob->cancel($request->message);

        return redirect()->route('interpreter-jobs.index')->with('success', 'Job Cancelled');
    }
}
