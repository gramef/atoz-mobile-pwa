<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClientRequest;
use App\Client;
use App\User;
use App\InterpreterJob;
use App\TranslatorJob;
use App\Organisation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\JobMail;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\ClientRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClientRequest $request)
    {
        Log::info(json_encode(['Creating new job request' => $request->__toString()]));

        $user = User::create(array_merge($request->all(), ['enabled' => true]));
        $user->assignRole('new-client');

        Log::info("Created new user with ID: $user->id");

        $data = $request->merge([
            'user_id' => $user->id
        ])->all();

        $client = Client::create($data);
        $client->contactMethods()->attach($request->contact_method);

        Log::info("Created new client with ID: $client->id");

        $data = $request->merge([
            'client_id' => $client->id
        ])->all();

        if ($request->is_organisation) {
            Organisation::create($data);
            Log::info(json_encode(["Created organisation for client: $client->id" => $data]));
        }

        if ($request->job_type == 'interpreter') {
            $interpreterJobData = $request->merge([
                'client_id' => $client->id,
                'from_language_id' => 37
            ])->all();

            $job = InterpreterJob::create($interpreterJobData);
            Log::info(json_encode(['Created interpeter job' => $interpreterJobData]));
            Mail::to(env('TO_EMAIL'))->send(new JobMail($job, 'emails.admin.interpreterJobRequest', 'New Client & Job Request', 'admin'));
        } else {
            $files = $request->file;

            foreach($files['path'] as $key => $path) {
                $documents[] = [
                    'name' =>  $files['name'][$key],
                    'url' => $path
                ];
            }

            $job = TranslatorJob::create($data);
            Log::info(json_encode(['Created translator job' => $job]));
            $job->documents()->createMany($documents);
            Mail::to(env('TO_EMAIL'))->send(new JobMail($job, 'emails.admin.translatorJobRequest', 'New Client & Job Request', 'admin'));
        }

        return response()->json(['msg' => 'Successfully created client']);
    }

    public function show(Client $client){

        $company = $client->organisation->company->name ?? '';
        return Response::json([
            'id' => $client->id,
            'name' => $client->name,
            'company' => $company,
        ]);
    }

}
