<?php

namespace App\Http\Controllers;

use App\Skill;
use App\User;
use App\Client;
use App\Agent;
use App\Company;
use App\Language;
use App\InterpreterType;
use App\Exports\InterpreterJobsExport;
use App\SecurityType;
use App\Mail\JobMail;
use App\AllUpdates;
use App\InterpreterJob;
use App\Jobs\MatchAgents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\InterpreterJobRequest;
use App\Traits\LogsUpdates;
use App\Exports\ExportData;
use App\Http\Requests\InterpreterBulkJobRequest;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class InterpreterJobController extends Controller
{
    use LogsUpdates;
    public function index(Request $request)
    {

        $users = User::role('agent')->get()->mapWithKeys(function ($user) {
            return [$user->id => $user->full_name];
        });
        $query = InterpreterJob::hasEnabledUser()
        ->with([
            'toLanguage',
            'matchedAgents',
            'client.user',
            'agent.user',
            'client.organisation.company',
            'cancellation',
            'feedback'
        ])
        ->showableToUser(auth()->user())
        ->filter($request->only(
            'date',
            'search',
            'language_id',
            'status',
            'company',
            'client',
            'require_qualified',
            'agents',
            'dna',
            'retrn',
            'bulk_id'
            // 'skills'
        ));

        // Clone the query for counting purposes
        //$countQuery = clone $query;
        //$count = $countQuery->count();

        $count = $query->count();

        // Get paginated results
        $jobs = $query->orderBy('appointment_date', 'DESC')->paginate(10);
        // Attach the first matched agent ID for each job
        $jobs->map(function ($job) {
            $job->matchedAgentId = $job->matchedAgents->first()->id ?? null;
            return $job;
        });
        //dd($jobs);
        // Return view with all necessary data
        return view('interpreter-jobs.index', [
            'jobs' => $jobs,
            'count' => $count, // Add count here
            'languages' => Language::orderBy('name')->pluck('name', 'id'),
            'companies' => Company::orderBy('name')->pluck('name', 'id'),
            'clients' => Client::fullNames(),
            'statuses' => config('enums.statuses'),
            'require_qualified' => InterpreterType::orderBy('id')->pluck('name', 'id'),
        //   'skills' => Skill::where('type', 0)->pluck('skill', 'id'),
            'bulk_ids' => InterpreterJob::whereNotNull('bulk_id')
                        ->distinct('bulk_id') // Ensure bulk IDs are unique
                        ->orderBy('id')
                        ->pluck('bulk_id', 'bulk_id'),
            'agents' => $users,

        ]);
    }

    public function dna(InterpreterJob $interpreterJob)
    {
        $this->logUpdate(
            $interpreterJob->id,
            'interpreter-jobs',
            auth()->user()->id,
            $interpreterJob->agent_id,
            $interpreterJob->status,
            'Your Assigned job is updated As DNA'
        );

        $interpreterJob->status = 0;
        $interpreterJob->dna = true;

        // $interpreterJob->agent()->dissociate();
        // optional($interpreterJob->assignedMatched)->delete();

        $interpreterJob->save();
        Mail::to(config('app.to.address'))->send(new JobMail(
            $interpreterJob,
            'emails.admin.job-dna',
            'Job Updated As DNA',
            'admin'
        ));
        Mail::to($interpreterJob->client->user)->send(new JobMail(
            $interpreterJob,
            'emails.clients.job-dna',
            'Your job is updated As DNA',
            'client'
        ));


        return back()->with('success', 'Interpreter job marked as DNA.');
    }
    public function retrn(InterpreterJob $interpreterJob)
    {
        $this->logUpdate(
            $interpreterJob->id,
            'interpreter-jobs',
            auth()->user()->id,
            $interpreterJob->agent_id,
            $interpreterJob->status,
            'This job is updated As Return'
        );

        $interpreterJob->status = 0;
        $interpreterJob->retrn = true;

        // $interpreterJob->agent()->dissociate();
        // optional($interpreterJob->assignedMatched)->delete();

        $interpreterJob->save();
        Mail::to(config('app.to.address'))->send(new JobMail(
            $interpreterJob,
            'emails.admin.job-retrn',
            'Job Updated As Return',
            'admin'
        ));
        Mail::to($interpreterJob->client->user)->send(new JobMail(
            $interpreterJob,
            'emails.clients.job-retrn',
            'Your job is updated As Return',
            'client'
        ));


        return back()->with('success', 'Interpreter job marked as Return.');
    }

    public function getaddress(Request $request)
    {
        $query = $request->input('query');
        $field = $request->input('field');
        $queryBuilder = InterpreterJob::query()
        ->select('address_line_1', 'address_line_2', 'county', 'postcode'); // Select only required columns

        switch ($field) {
            case 'address_line_1':
                $queryBuilder->where('address_line_1', 'LIKE', "%{$query}%");
                break;
            case 'address_line_2':
                $queryBuilder->where('address_line_2', 'LIKE', "%{$query}%");
                break;
            case 'county':
                $queryBuilder->where('county', 'LIKE', "%{$query}%");
                break;
            case 'postcode':
                $queryBuilder->where('postcode', 'LIKE', "%{$query}%");
                break;
        }
        $address = $queryBuilder->limit(10)->get();

        return response()->json($address);

    }
    public function create()
    {
        return view( 'interpreter-jobs.create', [
            'languages' => Language::orderBy('name')->pluck('name', 'id'),
            'clients' => Client::fullNames(),
            'skills' => Skill::where('type', 0)->pluck('skill', 'id'),
            'interpreter_types' => InterpreterType::orderBy('id')->pluck('name', 'id'),
            'security_types' => SecurityType::orderBy('id')->pluck('name', 'id'),
            'bulk_ids' => InterpreterJob::whereNotNull('bulk_id')
                ->distinct('bulk_id') // Ensure bulk IDs are unique
                ->orderBy('id')
                ->pluck('bulk_id'),
        ]);
    }
    public function create_bulk()
    {
        return view('interpreter-jobs.create_bulk', [
            'languages' => Language::orderBy('name')->pluck('name', 'id'),
            'clients' => Client::fullNames(),
            'skills' => Skill::where('type', 0)->pluck('skill', 'id'),
            'interpreter_types' => InterpreterType::orderBy('id')->pluck('name', 'id'),
            'security_types' => SecurityType::orderBy('id')->pluck('name', 'id'),
            'bulk_ids' => InterpreterJob::whereNotNull('bulk_id')
                ->distinct('bulk_id') // Ensure bulk IDs are unique
                ->orderBy('id')
                ->pluck('bulk_id'),
        ]);
    }
    public function store(InterpreterJobRequest $request)
    {
        //print_r($request);
        // dd($request);
        InterpreterJob::create($request->validated());

        return redirect()->route('interpreter-jobs.index')->with('success', 'Created interpreter job');
    }

    public function store_bulk(InterpreterBulkJobRequest $request)
    {
        // Prepare data for bulk insert
        // dd($request->all());
        $bulkData = [];
        foreach ($request->appointment_date as $key => $appointmentDate) {
            $bulkData[] = [
                'appointment_date' => $appointmentDate,
                'start_time' => $request->start_time[$key],
                'duration_hours' => $request->duration_hours[$key],
                'duration_minutes' => $request->duration_minutes[$key],
           'requested_agent_id' => $request->requested_agent_id[$key] ?? null,
                'to_language_id' => $request->to_language_id,
                'skill_id' => $request->skill_id,
                'require_qualified' => $request->require_qualified,
                'security_type_id' => $request->security_type_id,
                'gender' => $request->gender,
                'client_reference' => $request->client_reference,
                'user_title' => $request->user_title,
                'user_first_name' => $request->user_first_name,
                'user_last_name' => $request->user_last_name,
                'personal_identity_number' => $request->personal_identity_number,
                'department' => $request->department,
                'address_line_1' => $request->address_line_1,
                'address_line_2' => $request->address_line_2,
                'county' => $request->county,
                'postcode' => $request->postcode,
                'contact_information' => $request->contact_information,
                'special_requirements' => $request->special_requirements,
                'file_reference' => $request->file_reference,
                'date_of_birth' => $request->date_of_birth,
                'client_id' => $request->client_id,
                'service_user_required' => $request->service_user_required,
                'contact_information_is_same_as_account' => $request->contact_information_is_same_as_account,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Use a database transaction for safety
        DB::transaction(function () use ($bulkData) {
            InterpreterJob::insert($bulkData);
        });

        return redirect()->route('interpreter-jobs.index')->with('success', 'Created interpreter jobs successfully');
    }
    /*
    public function store_bulk(Request $request)
    {

        foreach ($request->appointment_date as $key => $value) {
            InterpreterJob::create([

                'appointment_date' => $request->appointment_date[$key],
                'start_time' => $request->start_time[$key],
                'duration_hours' => $request->duration_hours[$key],
                'duration_minutes' => $request->duration_minutes[$key],
                'to_language_id' => $request->to_language_id,
                'skill_id' => $request->skill_id,
                'requested_agent_id' => $request->requested_agent_id,
                'require_qualified' => $request->require_qualified,
                'security_type_id' => $request->security_type_id,
                'gender' => $request->gender,
                'client_reference' => $request->client_reference,
                'user_title' => $request->user_title,
                'user_first_name' => $request->user_first_name,
                'user_last_name' => $request->user_last_name,
                'personal_identity_number' => $request->personal_identity_number,
                'department' => $request->department,
                'address_line_1' => $request->address_line_1,
                'address_line_2' => $request->address_line_2,
                'county' => $request->county,
                'postcode' => $request->postcode,
                'contact_information' => $request->contact_information,
                'special_requirements' => $request->special_requirements,
                'file_reference' => $request->file_reference,
                'date_of_birth' => $request->date_of_birth,
                'client_id' => $request->client_id,
                'service_user_required' => $request->service_user_required,
                'contact_information_is_same_as_account' => $request->contact_information_is_same_as_account,
                ]);
        }

        return redirect()->route('interpreter-jobs.index')->with('success', 'Created interpreter job');
    }*/

    public function show(InterpreterJob $interpreterJob)
    {
        return view('interpreter-jobs.show', [
            'interpreterJob' => $interpreterJob,
        ]);
    }

    public function edit( InterpreterJob $interpreterJob )
    {
        return view( 'interpreter-jobs.edit', [
            'interpreterJob' => $interpreterJob,
            'languages' => Language::orderBy('name')->pluck('name', 'id'),
            'interpreter_types' => InterpreterType::orderBy('id')->pluck('name', 'id'),
            'security_types' => SecurityType::orderBy('id')->pluck('name', 'id'),
            'clients' => Client::fullNames(),
            'skills' => Skill::where('type', 0)->pluck('skill', 'id'),
            'bulk_ids' => InterpreterJob::whereNotNull('bulk_id')
                    ->distinct('bulk_id') // Ensure bulk IDs are unique
                    ->orderBy('id')
                    ->pluck('bulk_id'),
        ]);
    }

    public function update(InterpreterJobRequest $request, InterpreterJob $interpreterJob)
    {
        $interpreterJob->fill($request->validated());

        if (!$interpreterJob->isDirty()) {
            $this->logUpdate(
                $interpreterJob->id,
                'interpreter-jobs',
                auth()->user()->id,
                $interpreterJob->agent_id,
                $interpreterJob->status,
                'General Information is Updated'
            );
            return back()->with('success', 'Updated interpreter job');
        }

        if (!$interpreterJob->agent) {

            $interpreterJob->save();

            MatchAgents::dispatch($interpreterJob);

            return back()->with('success', 'Updated interpreter job');
        }

        if (!$interpreterJob->shouldBeRematched() && !$interpreterJob->isDirty(['appointment_date','appointment_time', 'duration_hours', 'duration_minutes'])) {

            $interpreterJob->save();
            $this->logUpdate(
                $interpreterJob->id,
                'interpreter-jobs',
                auth()->user()->id,
                $interpreterJob->agent_id,
                $interpreterJob->status,
                'Job updated without rematch required'
            );

            //            optional($interpreterJob->assignedMatched)->update(['status' => 0]);

            return back()->with('success', 'Updated interpreter job');
        }

        $interpreterJob->status = 0;

        Mail::to($interpreterJob->agent->user)->send(new JobMail(
            $interpreterJob,
            'emails.agents.interpreter-job-cancelled',
            'Job cancelled',
            'agent'
        ));

        optional($interpreterJob->assignedMatched)->delete();
        $interpreterJob->agent()->dissociate();
        $interpreterJob->save();

        MatchAgents::dispatch($interpreterJob);

        $this->logUpdate(
            $interpreterJob->id,
            'interpreter-jobs',
            auth()->user()->id,
            $interpreterJob->agent_id,
            $interpreterJob->status,
            'Job updated with agent dissociated'
        );

        return back()->with('success', 'Updated interpreter job');
    }

    public function allupdates(InterpreterJob $interpreterJob)
    {
        //    $job_id=$interpreterJob->id;
        //      $data= AllUpdates::where('job_id', $job_id)->with(['user:id,first_name,last_name'])
        //      ->get();
        //   echo "<pre>";  print_r($data->first_name);echo "</pre>";exit;
        $job_id = $interpreterJob->id;
        return view('interpreter-jobs.allupdates.index', [
            'interpreterJob' => $interpreterJob,
            'job_update' => AllUpdates::where([
                ['job_id', $job_id],
                ['deleted','N']])
            ->with(['user:id,first_name,last_name'])->get()
            // 'agent'=> function($query) {
            //     $query->select('id', 'first_name', 'last_name', 'email'); // Specify columns from agent table
            // }])


        ]);
    }
    public function filtered_export_data()
    {
        $data = [
            ['ID' => 1, 'Name' => 'John Doe', 'Email' => 'john@example.com', 'Created At' => now(), 'Updated At' => now()],
            ['ID' => 2, 'Name' => 'Jane Doe', 'Email' => 'jane@example.com', 'Created At' => now(), 'Updated At' => now()],
            // Add more rows as needed
        ];

        $columns = array('ID','Interpreter Name','Date of Session','Scheduled Start Time','Scheduled End Time','Scheduled Duration','Actual Start Time','Actual End Time','Actual Duration','Language','Gender Requirement','Delivery Address 1','Delivery Address 2','Delivery Address 3','Delivery Address 4','Delivery Address 5','County','Postcode','Provider Invoice Number','Timesheet Status','Timesheet Submission Date & Time','Extended Time Requested','Basic Cost','Additional Costs','Penalty Deduction','Total Cost');

        // $data=AllUpdates::where([
        //     ['job_id', 8509],
        //     ['deleted','N']])
        // ->with(['user:id,first_name,last_name'])->get();
        $xcel_sheet_name = 'Interpreter-jobs-'.date('Y-m-d').'.xlsx';
        //   $data=$this->objectToArray($data);
        return Excel::download(new ExportData($data, $columns), $xcel_sheet_name);






    }
    //     public function export()
    //     {

    //         $jobs = InterpreterJob::hasEnabledUser()
    //             ->with([
    //                 'toLanguage',
    //                 'matchedAgents',
    //                 'client.user',
    //                 'agent.user',
    //                 'client.organisation.company',
    //                 'cancellation',
    //             ])
    //             ->visibleToUser(auth()->user())
    //             ->orderBy('appointment_date', 'DESC')
    //             ->get();

    //         $headers = array(
    //             "Content-type"        => "text/csv",
    //             "Content-Disposition" => "attachment; filename=jobs.csv",
    //             "Pragma"              => "no-cache",
    //             "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
    //             "Expires"             => "0"
    //         );

    //         $columns = array('ID','Interpreter Name','Date of Session','Scheduled Start Time','Scheduled End Time','Scheduled Duration','Actual Start Time','Actual End Time','Actual Duration','Language','Gender Requirement','Delivery Address 1','Delivery Address 2','Delivery Address 3','Delivery Address 4','Delivery Address 5','County','Postcode','Provider Invoice Number','Timesheet Status','Timesheet Submission Date & Time','Extended Time Requested','Basic Cost','Additional Costs','Penalty Deduction','Total Cost');

    //         $callback = function() use($jobs, $columns) {
    //             $file = fopen('php://output', 'w');
    //             fputcsv($file, $columns);
    //             foreach ($jobs as $job) {
    //                 $agent = '';
    //                 if(isset($job->agent)) {
    //                     if (isset($job->agent->user)) {
    //                         $agent = $job->agent->user->full_name;
    //                     } else {
    //                         $agent = "DELETED USER";
    //                     }
    //                 }

    //                 $row['ID'] = $job->id;
    //                 $row['Interpreter Name'] =$agent;
    //                 $row['Date of Session'] = $job->appointment_date;
    //                 $row['Scheduled Start Time'] = $job->start_time;
    //                 $row['Scheduled End Time'] = $job->end_time;
    //                 $row['Scheduled Duration'] = $job->duration_hours .':'.$job->duration_minutes;
    //                 $row['Actual Start Time'] = '';
    //                 $row['Actual End Time'] = '';
    //                 $row['Actual Duration'] = '';
    //                 $row['Language'] = $job->toLanguage->name;
    //                 $row['Gender Requirement'] = $job->getGenderName();
    //                 $row['Delivery Address 1'] = $job->department;
    //                 $row['Delivery Address 2'] = $job->address_line_1;
    //                 $row['Delivery Address 3'] = $job->address_line_2;
    //                 $row['Delivery Address 4'] = '';
    //                 $row['Delivery Address 5'] = '';
    //                 $row['County'] = $job->county;
    //                 $row['Postcode'] = $job->postcode;
    //                 $row['Provider Invoice Number'] = $job->client_reference;
    //                 $row['Timesheet Status'] = '';
    //                 $row['Timesheet Submission Date & Time'] = '';
    //                 $row['Extended Time Requested'] = '';
    //                 $row['Basic Cost'] = '';
    //                 $row['Additional Costs'] = '';
    //                 $row['Penalty Deduction'] = '';
    //                 $row['Total Cost'] = '';

    //                 fputcsv($file, array(
    //                     $row['ID'],
    //                     $row['Interpreter Name'],
    //                     $row['Date of Session'],
    //                     $row['Scheduled Start Time'],
    //                     $row['Scheduled End Time'],
    //                     $row['Scheduled Duration'],
    //                     $row['Actual Start Time'],
    //                     $row['Actual End Time'],
    //                     $row['Actual Duration'],
    //                     $row['Language'],
    //                     $row['Gender Requirement'],
    //                     $row['Delivery Address 1'],
    //                     $row['Delivery Address 2'],
    //                     $row['Delivery Address 3'],
    //                     $row['Delivery Address 4'],
    //                     $row['Delivery Address 5'],
    //                     $row['County'],
    //                     $row['Postcode'],
    //                     $row['Provider Invoice Number'],
    //                     $row['Timesheet Status'],
    //                     $row['Timesheet Submission Date & Time'],
    //                     $row['Extended Time Requested'],
    //                     $row['Basic Cost'],
    //                     $row['Additional Costs'],
    //                     $row['Penalty Deduction'],
    //                     $row['Total Cost'],
    //                 ));
    //             }

    //             fclose($file);
    //         };

    //         return response()->stream($callback, 200, $headers);

    // //
    // //
    // //            ,
    // //            'languages' => Language::pluck('name', 'id')'
    // //            'companies' => Company::pluck('name', 'id'),
    // //            'clients' => Client::fullNames(),
    // //            'statuses' => config('enums.statuses'),
    // //        ]);
    //     }
    public function export(Request $request)
    {
        // $jobs = InterpreterJob::hasEnabledUser()
        //     ->with([
        //         'toLanguage',
        //         'matchedAgents',
        //         'client.user',
        //         'agent.user',
        //         'client.organisation.company',
        //         'cancellation',
        //     ])
        //     ->visibleToUser(auth()->user())
        //     ->filter($request->only(
        //         'date',
        //         'search',
        //         'language_id',
        //         'status',
        //         'company',
        //         'client',
        //         'require_qualified',
        //           'agents'
        //     ))
        //     ->orderBy('appointment_date', 'DESC')
        //     ->get();
        //     // dd($jobs);
        // return Excel::download(new InterpreterJobsExport($jobs), 'interpreter_jobs.xlsx');
        $filters = $request->only(
            'date',
            'search',
            'language_id',
            'status',
            'company',
            'client',
            'require_qualified',
            'agents',
            'dna',
            'retrn'
        );

        return Excel::download(new InterpreterJobsExport($filters), 'interpreter_jobs.xlsx');


    }

}
