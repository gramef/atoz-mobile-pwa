<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Feedback;
use App\InterpreterJob;
use App\Timesheet;
use App\Mail\JobMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        // Start the query

        $feedbackQuery = Feedback::with(['interpreter.client.userSheet', 'agentOne.user']);

        if ($user->roles->contains('name', 'agent')) {
            // Apply filters for agent role
            $feedbackQuery->where('agent_id', $user->agent->id)
                          ->where('agent_status', 'Y'); // Ensure both conditions are applied
        } elseif ($user->roles->contains('name', 'client')) {
            // Apply filters for client role
            $feedbackQuery->whereHas('interpreter.client', function ($query) use ($user) {
                $query->where('client_id', $user->client->id);
            });
        }
        // \DB::enableQueryLog();
        // Paginate the results
        $feedbacks = $feedbackQuery->paginate(10);


        // Return the view with the feedbacks
        return view('feedback.index', compact('feedbacks'));
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'appearance' => 'required',
            'punctuality' => 'required',
            'quality' => 'required',
            'empathy' => 'required'
        ]);

        // Get the first feedback record for the given job_id
        $feedback = Feedback::where('job_id', $request['job_id'])->first();

        if (!$feedback) {
            // If no feedback exists, create a new one
            $feedback = new Feedback();
            $feedback->job_id = $request['job_id'];
            $feedback->client_id = $request['client_id'];
            $feedback->agent_id = $request['agent_id'];
        }
        $interpreterJob = InterpreterJob::findOrFail($request['job_id']);

        // Update or set the feedback attributes
        $feedback->appearance_rating = $validatedData['appearance'];
        $feedback->punctuality = $validatedData['punctuality'];
        $feedback->quality_of_interpreting = $validatedData['quality'];
        $feedback->empathy = $validatedData['empathy'];
        $feedback->comment = $request['comments'];

        Mail::to(config('app.to.address'))->send(new JobMail(
            $interpreterJob,
            'emails.admin.job-feedback',
            'Feedback',
            'admin'
        ));
        // Save the feedback (new or updated)
        $feedback->save();

        return redirect()->back()->with('success', 'Feedback added/updated successfully.');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $timesheets = Timesheet::with(['interpreter.client.userSheet','agentOne.user'])
        ->where('job_id', $id)
        ->get();

        $feedback = Feedback::where('job_id', $id)->first();

        return view('feedback.AddFeedBack', ['timesheet' => $timesheets,'feedback' => $feedback]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function updateStatus($id)
    {
        //    dd($status);
        $feedback = Feedback::findOrFail($id);

        $data['agent_status'] = 'Y';

        // Update the client_status
        $feedback->update($data);

        return redirect()->back()->with('success', "Agent status updated successfully.");
    }
}
