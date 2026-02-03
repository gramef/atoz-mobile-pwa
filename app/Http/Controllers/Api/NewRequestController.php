<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Mail\JobMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Api\NewClientRequest;

class NewRequestController extends Controller
{
    public function store(NewClientRequest $request)
    {
        $user = new User();

        $user
            ->fill($request->validated())
            ->fill(['enabled' => false, 'password' => str_random(60)])
            ->save();

        $user->assignRole('new-client');

        $client = $user->client()->create($request->validated());
        $client->contactMethods()->attach($request->contact_method);

        if ($request->is_organisation) {
            $client->organisation()->create($request->validated());
        }

        if ($request->job_type == 'interpreter') {
            $job = $client->interpreterJobs()->create($request->validated());
        } else {
            $job = $client->translatorJobs()->create($request->validated());

            $request_files = $request->file;
            $files = [];
            if (isset($request_files['path'][0])) {
                for($i=0; $i<count($request_files['path']); $i++){
                    $files[] = [
                        'name' => $request_files['name'][$i],
                        'url' => $request_files['path'][$i],
                    ];
                }
            }
            $job->documents()->createMany($files);
        }

        Mail::to(config('app.to.address'))->send(new JobMail(
            $job,
            "emails.$request->job_type-jobs.created",
            'New Client & Job Request',
            'admin'
        ));

        Mail::to($user)->send(new JobMail(
            $job,
            'emails.clients.new-request',
            'Job Request Confirmation',
            'client'
        ));
    }
}
