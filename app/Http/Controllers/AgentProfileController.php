<?php

namespace App\Http\Controllers;

use App\User;
use App\Skill;
use App\Language;
use App\ContactMethod;
use App\Mail\UserUpdatedMail;
use App\Http\Requests\AgentRequest;
use Illuminate\Support\Facades\Mail;

class AgentProfileController extends Controller
{
    public function create()
    {
        if (auth()->user()->agent()->exists()) {
            return redirect()->route('agents.profile.edit');
        }

        return view('agents.profile.create', [
            'skills' => Skill::all(),
            'languages' => Language::pluck('name', 'id'),
            'contactMethods' => ContactMethod::all(),
        ]);
    }

    public function store(AgentRequest $request)
    {
        if (auth()->user()->agent()->exists()) {
            return redirect()->route('agents.profile.edit');
        }

        $user = User::where('email', $request->user()->email)->first();

        tap($user)
            ->update($request->validated())
            ->syncRoles(array_merge($request->interpreter_types, ['new-agent']));

        $agent = $user->agent()->create($request->validated());
        $agent->skills()->sync($request->skills);
        $agent->contactMethods()->sync($request->contact_method);
        $agent->setLanguages($request->languages);

        return redirect()->route('agents.profile.edit')->with('success', 'Created profile');
    }

    public function edit()
    {
        if (!auth()->user()->agent()->exists()) {
            return redirect()->route('agents.profile.create');
        }

        return view('agents.profile.edit', [
            'agent' => auth()->user()->agent,
            'skills' => Skill::all(),
            'languages' => Language::pluck('name', 'id'),
            'contactMethods' => ContactMethod::all(),
        ]);
    }

    public function update(AgentRequest $request)
    {
        $user = $request->user();
        $user->fill($request->validated());


        if ($user->hasRole('new-agent')) {
            $user->save();
            $user->syncRoles(array_merge($request->interpreter_types, ['new-agent']));

            $user->agent->update($request->validated());
            $user->agent->skills()->sync($request->skills);
            $user->agent->contactMethods()->sync($request->contact_method);
            $user->agent->setLanguages($request->languages);

            return back()->with('success', 'Updated profile');
        }
        $user->agent->fill(
            $request->only([
                'contact_number',
                'address_line_1',
                'address_line_2',
                'county',
                'postcode',
                ]),
        );

        if (!$user->isDirty() && !$user->agent->isDirty()) {
            return back()->with('success', 'Updated profile');
        }

        Mail::to(config('app.to.address'))->send(new UserUpdatedMail(
            $user,
            'emails.agents.updated',
            'An agent has updated their profile'
        ));

        tap($user)
                ->removeRole('agent')
                ->assignRole('new-agent');
        $user->push();

        return back()->with('success', 'Updated profile');
    }
}
