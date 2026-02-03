<?php

namespace App\Http\Controllers;

use App\Agent;
use App\Language;
use Illuminate\Http\Request;
use App\Http\Requests\AgentRequest;
use Illuminate\Support\Facades\Mail;

class NewAgentController extends Controller
{
    public function index(Request $request)
    {
        return view('agents.new.index', [
            'agents' => Agent::new()
                ->hasEnabledUser()
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

    public function update(AgentRequest $request, Agent $agent)
    {
        if (!$agent->hasDbsFields()) {
            return back()->withErrors(['This agent does not have their DBS fields filled in.']);
        }

        tap($agent->user)
            ->update($request->validated())
            ->syncRoles(array_merge($request->interpreter_types, ['agent']));

        $agent->update($request->validated());
        $agent->skills()->sync($request->skills);
        $agent->contactMethods()->sync($request->contact_method);
        $agent->setLanguages($request->languages);

        Mail::send('emails.agents.approved', [], function ($m) use ($agent) {
            $m->from(config('mail.from.address'));
            $m->to($agent->user->email)->subject('Agent account approved');
        });

        return back()->with('success', 'Agent approved');
    }
}