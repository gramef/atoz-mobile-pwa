<?php

namespace App\Http\Controllers;

use App\User;
use App\Client;
use App\Company;
use App\ContactMethod;
use Illuminate\Http\Request;
use App\Mail\UserCreatedMail;
use App\Http\Requests\ClientRequest;
use Illuminate\Support\Facades\Mail;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::hasEnabledUser()
            ->with([
                'user',
                'organisation',
                'organisation.company',
                'interpreterJobs' => function ($q) {
                        $q->active();
                    },
                    'translatorJobs' => function ($q) {
                        $q->active();
                    }
            ])
            ->filter($request->only(['company','search']))
            ->paginate(10);

        return view('clients.index', [
           'clients' => $clients,
           'companies' => Company::pluck('name','id')
        ]);
    }

    public function create()
    {
        return view('clients.create', [
            'contactMethods' => ContactMethod::all(),
            'companies' => Company::pluck('name', 'id'),
        ]);
    }
    public function toggleShowAgents(Client $client)
    {
        \Log::info('Before toggle: show_agents = ' . $client->show_agents);
        // Toggle the show_agents value
        $client->show_agents = !$client->show_agents;
         if ($client->save()) {
        \Log::info('After toggle: show_agents = ' . $client->show_agents);
    } else {
        \Log::error('Failed to save client.');
    }

        // Redirect back with a success message
        return redirect()->back()->with('status', 'Agent details visibility updated!');
    }
    public function store(ClientRequest $request)
    {
        $user = new User();

        $user
            ->fill($request->validated())
            ->fill(['enabled' => true, 'password' => str_random(60)])
            ->save();

        $user->assignRole('client');

        $user->client()
            ->create($request->validated())
            ->contactMethods()
            ->attach($request->contact_method);

        if ($request->is_organisation) {
            $user->client->organisation()->create($request->validated());
        }

        Mail::to($user)->send(new UserCreatedMail(
            app('auth.password.broker')->createToken($user),
            $user,
            'emails.users.created',
            'Client Account created'
        ));

        return redirect()->route('clients.index')->with('success', 'Client created');
    }

    public function edit(Client $client)
    {
        return view('clients.edit', [
            'client' => $client,
            'contactMethods' => ContactMethod::all(),
            'companies' => Company::pluck('name', 'id'),
        ]);
    }

    public function update(ClientRequest $request, Client $client)
    {
        $client->user->update($request->validated());

        if ($request->is_organisation) {
            $client->organisation()->create($request->validated());
        } else if (!$request->is_organisation){
            $client->organisation()->delete();
        }

        tap($client)
            ->update($request->validated())
            ->contactMethods()
            ->sync($request->contact_method);

        optional($client->organisation)->update($request->validated());

        return back()->with('success', 'Client updated');
    }

    public function destroy(Client $client)
    {
        $client->user->delete();
        $client->organisation()->delete();
        $client->interpreterJobs()->delete();
        $client->translatorJobs()->delete();
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client deleted');
    }
}
