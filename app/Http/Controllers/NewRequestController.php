<?php

namespace App\Http\Controllers;

use App\Client;
use App\Company;
use App\Jobs\MatchAgents;

class NewRequestController extends Controller
{
    public function index()
    {
        return view('clients.new-requests.index', [
            'clients' => Client::whereHas('user', function ($query) {
                $query->role('new-client')->where('enabled', 0);
            })
                ->where('rejected', false)
                ->with([
                    'user',
                    'organisation',
                    'interpreterJobs',
                ])
                ->paginate(10),
            'companies' => Company::pluck('name', 'id'),
        ]);
    }
    
    public function update(Client $client)
    {
        tap($client->user)->update([
            'enabled' => 1,
        ])->syncRoles(['client']);

        MatchAgents::dispatch($client->getRequestJob());

        return redirect()->route('clients.new-requests.index')->with('success', 'Request approved');
    }

    public function destroy(Client $client)
    {
        $client->user->delete();
        $client->organisation()->delete();
        $client->getRequestJob()->delete();
        $client->delete();

        return redirect()->route('clients.rejected.index')->with('success', 'Request deleted');
    }
}
