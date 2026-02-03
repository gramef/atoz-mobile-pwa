<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use App\Skill;
use App\Client;
use App\Company;
use App\InterpreterType;
use App\Language;
use App\Mail\JobMail;
use App\TranslatorJob;
use App\Jobs\MatchAgents;
use Illuminate\Http\Request;
use App\AllUpdates;
use App\Traits\LogsUpdates;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\TranslatorJobRequest;
use App\Exports\TranslatorJobsExport;
use App\Exports\ExportData;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class TranslatorJobController extends Controller
{
    use LogsUpdates;
    public function index(Request $request)
    {
        // dd("ss");
        return view('translator-jobs.index', [
            'jobs' => TranslatorJob::hasEnabledUser()
                ->with([
                    'fromLanguage',
                    'toLanguage',
                    'matchedAgents',
                    'client.user',
                    'agent.user',
                    'client.organisation.company',
                    'cancellation',
                ])
                ->visibleToUser(auth()->user())
                ->filter($request->only(
                    'date',
                    'search',
                    'language_id',
                    'status',
                    'company',
                    'client'
                    // 'require_qualified'
                ))
                ->orderBy('target_date', 'DESC')
                ->paginate(10),
            'languages' => Language::pluck('name', 'id'),
            'companies' => Company::pluck('name', 'id'),
            'clients' => Client::fullNames(),
            'statuses' => config('enums.statuses')
            //  'require_qualified' => InterpreterType::where('id','=','5')
            // ->orderBy('id')
            // ->pluck('name', 'id'),
        ]);
    }

    public function create()
    {
        return view('translator-jobs.create', [
            'languages' => Language::orderBy('name')->pluck('name', 'id'),
            'clients' => Client::fullNames(),
            'skills' => Skill::where('type', 1)->pluck('skill', 'id'),
        ]);
    }


    public function allupdates(TranslatorJob $translatorJob)
    {

        $job_id = $translatorJob->id;
        return view('translator-jobs.allupdates.index', [
                'translatorJob' => $translatorJob,
            'job_update' => AllUpdates::where([
                ['job_id', $job_id],
                ['job_type', 'translator-jobs'],
                ['deleted','N']])
            ->with(['user:id,first_name,last_name'])->get()


        ]);
    }
    public function store(TranslatorJobRequest $request)
    {
        TranslatorJob::create($request->validated())
            ->documents()
            ->createMany($request->documents);

        return redirect()->route('translator-jobs.index')->with('success', 'Created translator job');
    }

    public function show(TranslatorJob $translatorJob)
    {
        return view('translator-jobs.show', [
            'translatorJob' => $translatorJob,
        ]);
    }

    public function edit(TranslatorJob $translatorJob)
    {
        return view('translator-jobs.edit', [
            'translatorJob' => $translatorJob,
            'languages' => Language::orderBy('name')->pluck('name', 'id'),
            'clients' => Client::fullNames(),
            'skills' => Skill::where('type', 1)->pluck('skill', 'id'),
        ]);
    }

    public function update(TranslatorJobRequest $request, TranslatorJob $translatorJob)
    {
        if ($request->filled('documents')) {
            $translatorJob->documents()->createMany($request->documents);
            $translatorJob->documentUploaded = true;
            $translatorJob->setUpdatedAt($translatorJob->freshTimestamp());
        }

        $translatorJob->fill($request->validated());

        if (!$translatorJob->isDirty()) {
            return back()->with('success', 'Updated translator job');
        }

        if (!$translatorJob->agent) {
            $this->logUpdate(
                $translatorJob->id,
                'translator-jobs',
                auth()->user()->id,
                $translatorJob->agent_id,
                $translatorJob->status,
                'Job is updated'
            );

            $translatorJob->save();

            MatchAgents::dispatch($translatorJob);

            return back()->with('success', 'Updated translator job');
        }

        if (!$translatorJob->shouldBeRematched()) {

            $translatorJob->save();

            optional($translatorJob->assignedMatched)->update(['status' => 0]);
            $this->logUpdate(
                $translatorJob->id,
                'translator-jobs',
                auth()->user()->id,
                $translatorJob->agent_id,
                $translatorJob->status,
                'Should be Rematched'
            );

            return back()->with('success', 'Updated translator job');
        }

        $translatorJob->status = 0;

        Mail::to($translatorJob->agent->user)->send(new JobMail(
            $translatorJob,
            'emails.agents.translator-job-cancelled',
            'Job cancelled',
            'agent'
        ));
        $this->logUpdate(
            $translatorJob->id,
            'translator-jobs',
            auth()->user()->id,
            $translatorJob->agent_id,
            $translatorJob->status,
            'translation job cancelled'
        );

        optional($translatorJob->assignedMatched)->delete();
        $translatorJob->agent()->dissociate();
        $translatorJob->save();

        MatchAgents::dispatch($translatorJob);

        return back()->with('success', 'Updated translator job');
    }
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
            'agents'
        );
        $now = Carbon::now()->timezone('Europe/London');
        $date = $now->format('Y-m-d');
        return Excel::download(new TranslatorJobsExport($filters), "translator_jobs-$date.xls");


    }
}
