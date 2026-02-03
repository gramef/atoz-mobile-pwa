<?php

namespace App\Http\Controllers;

use App\Agent;
use App\Skill;
use App\Language;
use App\ContactMethod;
use Illuminate\Http\Request;

class ArchivedAgentController extends Controller
{
    public function index(Request $request)
    {
        return view('agents.archived.index', [
            'agents' => Agent::hasDisabledUser()
                ->filter($request->only('name', 'language', 'agent_type'))
                ->with([
                    'user',
                    'user.roles',
                    'languages',
                ])
                ->paginate(10),
            'languages' => Language::pluck('name', 'id'),
        ]);
    }

    public function update(Agent $agent)
    {
        $agent->user->update(['enabled' => 0]);

        return redirect()->route('agents.index')->with('success', 'Agent archived');
    }

    public function show(Agent $agent)
    {
        return view('agents.archived.show', [
            'agent' => $agent,
            'skills' => Skill::all(),
            'languages' => Language::pluck('name', 'id'),
            'contactMethods' => ContactMethod::all(),
        ]);
    }

    public function destroy(Agent $agent)
    {
        $agent->user->update(['enabled' => 1]);

        return redirect()->route('agents.archived.index')->with('success', 'Agent restored');
    }
}
