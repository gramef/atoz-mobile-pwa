<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Timesheet;
use App\Language;
use App\InterpreterJob;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use App\Feedback;
use App\Mail\JobMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class TimeSheetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();


        $query = Timesheet::with(['interpreter.client.userSheet', 'agentOne.user']);

        // Filter by agent if the user has the "agent" role
        if ($user->roles->contains('name', 'agent')) {
            $query->where('agent_id', $user->agent->id);
        }

        // Filter by client if the user has the "client" role
        if ($user->roles->contains('name', 'client')) {
            $query->whereHas('interpreter.client', function ($query) use ($user) {
                $query->where('client_id', $user->client->id);
            });
        }

        // Apply client_name filter using the scope
        if ($request->has('client_name') && $request->client_name != '') {
            $query->filterByClientName($request->client_name);
        }

        // Apply agent_name filter using the scope
        if ($request->has('agent_name') && $request->agent_name != '') {
            $query->filterByAgentName($request->agent_name);
        }

        // Apply ref (job_id) filter using the scope
        if ($request->has('ref') && $request->ref != '') {
            $query->filterByRef($request->ref);
        }


        // Paginate the results
        $timesheets = $query->paginate(10);

        return view('timesheet.index', [
        'timesheets' => $timesheets,
    ]);
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

        $data = $request->validate([
            'signature' => 'required'
        ]);



        $signatureData = $request->input('signature');

        $signatureData = str_replace('data:image/png;base64,', '', $signatureData);
        $signatureData = str_replace(' ', '+', $signatureData);
        $signature = base64_decode($signatureData);


        dd($signature);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        dd($id);
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

        return view('timesheet.agentSignature', ['timesheets' => $timesheets,'feedback' => $feedback]);

        // dd($id);
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

        // Validate the incoming request data
        $validatedData = $request->validate([
            'signature' => 'required',
            // 'agent_start_time' => 'required',
            // 'agent_end_time' => 'required',
            'duration_hours' => 'required|integer',
            'duration_minutes' => 'required|integer',
        ]);


        // Find the timesheet by ID
        $timesheet = Timesheet::findOrFail($id);
        $interpreterJob = InterpreterJob::findOrFail($timesheet->job_id);
        //  dd($request->client_name);

        // Extract validated data
        // dd($validatedData);
        $signatureData = $validatedData['signature'];
        $duration_hours = $validatedData['duration_hours'];
        $duration_minutes = $validatedData['duration_minutes'];
        $agent_start_time = $request->agent_start_time;
        $agent_end_time = $request->agent_end_time;


        // Check if the authenticated user is an agent
        if (isset(auth()->user()->agent->id)) {
            // Update the timesheet data for an agent
            $updateData = [
                'agent_signature' => $signatureData,
                'agent_duration_hours' => $duration_hours,
                'agent_duration_minutes' => $duration_minutes,
                'agent_start_time' => $agent_start_time,
                'agent_end_time' => $agent_end_time,
            ];
            Mail::to($interpreterJob->client->user)->send(new JobMail(
                $interpreterJob,
                'emails.clients.job-timesheet-sign',
                'Timesheet needs to be signed',
                'client'
            ));



        } else {

            // Update the timesheet data for a client
            // Validate the incoming request data
            $validatedData = $request->validate([

                'client_name' => 'required',
                // 'agent_start_time' => 'required',
                // 'agent_end_time' => 'required',
                'client_phone' => 'required|numeric',
                'client_designation' => 'required',
            ]);
            $client_name = $request->client_name;
            $client_phone = $request->client_phone;
            $client_designation = $request->client_designation;
            $updateData = [
                'client_signature' => $signatureData,
                'client_duration_hours' => $duration_hours,
                'client_duration_minutes' => $duration_minutes,
                'client_designation' => $client_designation,
                'client_name' => $client_name,
                'client_phone' => $client_phone,

            ];
            Mail::to(config('app.to.address'))->send(new JobMail(
                $interpreterJob,
                'emails.admin.job-timesheet-sign',
                'Timesheet Signed',
                'admin'
            ));
        }

        // Update the timesheet with the new data
        $timesheet->update($updateData);

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Timesheet updated Successfully.');
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

    public function updateStatus($id, $status)
    {
        //    dd($status);
        $timesheet = Timesheet::findOrFail($id);
        if ($status == 'YA') {
            $data['agent_status'] = 'Y';
            $user = "Agent";
        } else {
            $data['client_status'] = 'Y';
            $user = "Client";
        }
        // Update the client_status
        $timesheet->update($data);

        return redirect()->back()->with('success', "$user status updated successfully.");
    }

    public function generateTimesheet($id)
    {

        $timesheet = Timesheet::with(['interpreter.client.userSheet', 'agentOne.user'])
    ->where('id', $id) // Add your where clause here
    ->firstOrFail();

        //   dd($timesheet);
        $html = view('timesheet/timesheet_pdf', compact('timesheet'))->render();
        // echo $html; die;
        // Initialize Dompdf

        // $mpdf = new Mpdf();
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_top' => 5,
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_bottom' => 5,
        ]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('example.pdf', 'I');
        die;


    }
}
