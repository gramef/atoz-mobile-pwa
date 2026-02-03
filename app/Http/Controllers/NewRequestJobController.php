<?php

namespace App\Http\Controllers;

use App\Skill;
use App\Client;
use App\Language;
use Illuminate\Http\Request;

class NewRequestJobController extends Controller
{
    public function edit(Client $client)
    {
        return view('clients.new-requests.job.edit', [
            'client' => $client,
            'job' => $client->getRequestJob(),
            'languages' => Language::pluck('name', 'id'),
            'clients' => Client::fullNames($client->id),
            'skills' => $client->interpreterJobs()->exists() ?
                Skill::where('type', 0)->pluck('skill', 'id') :
                Skill::where('type', 1)->pluck('skill', 'id'),
        ]);
    }

    public function update(Request $request, Client $client)
    {
        dd('job updated');
    }
}
