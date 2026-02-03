<?php

namespace App\Http\Controllers;

use App\InterpreterJob;

class InterpreterJobAgentController extends Controller
{
    public function index(InterpreterJob $interpreterJob)
    {
        return view( 'interpreter-jobs.agent.index', [
            'interpreterJob' => $interpreterJob,
            'agent' => $interpreterJob->agent,
        ] );
    }
}
