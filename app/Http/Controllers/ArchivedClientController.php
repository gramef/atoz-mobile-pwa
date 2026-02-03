<?php

namespace App\Http\Controllers;

use App\Client;
use App\Company;
use Illuminate\Http\Request;

class ArchivedClientController extends Controller
{
    public function index(Request $request)
    {
        return view('clients.archived.index', [
            'clients' => Client::where('archived', true)
                ->with([
                    'user', 
                    'organisation.company'
                ])
                ->filter($request->only('company'))
                ->paginate(10),
            'companies' => Company::onlyTrashed()->pluck('name', 'id'),
        ]);
    }

    public function show(Client $client)
    {
        return view('clients.archived.show', [
            'client' => $client,
        ]);
    }

    public function update(Client $client)
    {
        if ($client->hasActiveJobs()) {
            return redirect()->route('clients.index')->withErrors(['Client has active jobs']);
        }

        tap($client)
            ->update(['archived' => 1])
            ->user
            ->update(['enabled' => 0]);

        return redirect()->route('clients.index')->with('success', 'Client archived');
    }

    public function destroy(Client $client)
    {
        tap($client)
            ->update(['archived' => 0])
            ->user
            ->update(['enabled' => 1]);

        return redirect()->route('clients.index')->with('success', 'Client restored');
    }
}
