<?php

namespace App\Http\Controllers;

use App\User;
use App\Agent;
use App\Skill;
use App\Language;
use App\ContactMethod;
use Illuminate\Http\Request;
use App\Mail\UserCreatedMail;
use App\Http\Requests\AgentRequest;
use Illuminate\Support\Facades\Mail;

class AgentController extends Controller
{
    public function index(Request $request)
    {
        // dd("not here");exit;
        return view('agents.index', [
            'agents' => Agent::hasEnabledUser()
                ->filter($request->only('name', 'email', 'language', 'agent_type'))
                ->with([
                    'user.roles',
                    'languages',
                ])
                ->paginate(10),
            'languages' => Language::pluck('name', 'id'),
        ]);
    }

    public function create()
    {
        return view('agents.create', [
            'skills' => Skill::all(),
            'languages' => Language::orderBy('name')->pluck('name', 'id'),
            'contactMethods' => ContactMethod::all(),
        ]);
    }

    public function store(AgentRequest $request)
    {
        $user = new User();

        $user
            ->fill($request->validated())
            ->fill(['enabled' => true, 'password' => str_random(60)])
            ->save();

        $user->assignRole(array_merge($request->interpreter_types, ['new-agent']));

        $agent = $user->agent()->create($request->validated());
        $agent->skills()->attach($request->skills);
        $agent->contactMethods()->attach($request->contact_method);
        $agent->setLanguages($request->languages);

        Mail::to($agent->user)->send(new UserCreatedMail(
            app('auth.password.broker')->createToken($agent->user),
            $agent->user,
            'emails.users.created',
            'Agent Account created'
        ));

        return redirect()->route('agents.index')->with('success', 'Created agent');
    }

    public function edit(Agent $agent)
    {
        return view('agents.edit', [
            'agent' => $agent,
            'skills' => Skill::all(),
            'languages' => Language::orderBy('name')->pluck('name', 'id'),
            'contactMethods' => ContactMethod::all(),
        ]);
    }

    public function update(AgentRequest $request, Agent $agent)
    {
        tap($agent->user)
            ->update($request->validated())
            ->syncRoles(array_merge($request->interpreter_types, ['agent']));

        $agent->update($request->validated());
        $agent->skills()->sync($request->skills);
        $agent->contactMethods()->sync($request->contact_method);
        $agent->setLanguages($request->languages);

        return back()->with('success', 'Updated agent');
    }

    public function destroy(Agent $agent)
    {
        $agent->user->delete();
        $agent->matchedJobs()->delete();
        $agent->delete();

        return redirect()->route('agents.archived.index')->with('success', 'Deleted agent');
    }
}