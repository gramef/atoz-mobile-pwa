<?php

namespace App\Http\Controllers;

use App\Company;
use App\ContactMethod;
use App\Http\Requests\ClientRequest;
use App\Mail\UserUpdatedMail;
use Illuminate\Support\Facades\Mail;

class ClientProfileController extends Controller
{
    public function edit()
    {
        return view('clients.profile.edit', [
            'client' => auth()->user()->client,
            'contactMethods' => ContactMethod::all(),
            'companies' => Company::pluck('name', 'id'),
        ]);
    }

    public function update(ClientRequest $request)
    {
        $user = $request->user();
        $validatedData = $request->validated();

        $user->fill($validatedData);
        $user->client->fill($validatedData);
        optional($user->client->organisation)->fill($validatedData);

        $contactMethodsWereChanged = $request->contact_method != $user->client->contactMethods->pluck('id')->all();

        if (!$contactMethodsWereChanged && !$user->isDirty() && !$user->client->isDirty() && !optional($user->client->organisation)->isDirty()) {
            return redirect()->back()->with('success', 'Updated profile');
        }

        Mail::to(config('app.to.address'))->send(new UserUpdatedMail(
            $user,
            'emails.clients.updated',
            'A client has updated their profile'
        ));

        $user->push();
        $user->client->contactMethods()->sync($request->contact_method);

        return redirect()->back()->with('success', 'Updated profile');
    }
}
