<?php

namespace App\Http\Controllers;

use App\Client;
use App\Company;
use App\ContactMethod;
use App\Http\Requests\ClientRequest;
use App\Jobs\MatchAgents;

class NewRequestClientController extends Controller
{
    public function edit(Client $client)
    {
        return view('clients.new-requests.client.edit', [
            'client' => $client,
            'contactMethods' => ContactMethod::all(),
            'companies' => Company::pluck('name', 'id'),
        ]);
    }

    public function update(ClientRequest $request, Client $client)
    {
        $client->user
            ->fill($request->validated())
            ->fill(['enabled' => true])
            ->save();

        $client->user->syncRoles(['client']);

        tap($client)
            ->update($request->validated())
            ->contactMethods()
            ->sync($request->contact_method);

        optional($client->organisation)->update($request->validated());

        MatchAgents::dispatch($client->getRequestJob());

        return redirect()->route('clients.new-requests.index')->with('success', 'Client approved');
    }
}
