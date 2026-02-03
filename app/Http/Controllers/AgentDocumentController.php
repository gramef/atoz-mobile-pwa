<?php

namespace App\Http\Controllers;

use App\Agent;
use App\Document;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\AgentDocumentRequest;
use App\Mail\AgentMail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class AgentDocumentController extends Controller
{
    public function edit(Agent $agent = null)
    {
        $returnRoute = 'agents.documents.edit';

        if (!$agent) {
            $agent = auth()->user()->agent;
            $returnRoute = 'agents.profile.documents.edit';
        }

        return view($returnRoute, [
            'agent' => $agent,
            'documents' => Document::select('name', 'url')->get(),
        ]);
    }

    public function update(AgentDocumentRequest $request, ?Agent $agent)
    {
        if (!$request->user()->hasRole('admin')) {
            $agent = $request->user()->agent;
        }

        $updatedDocuments = collect();

        if ($request->files->get('documents')) {
            foreach ($request->files->get('documents') as $key => $document) {
                $existingDocument = $agent->documents->firstWhere('type', config('enums.document_types')[$key]);

                if ($existingDocument !== null) {
                    Storage::disk('public')->delete($existingDocument->url);
                }

                $file = $request->file("documents.$key");

                $agent->documents()->updateOrCreate(
                    [
                        'type' => config('enums.document_types')[$key],
                    ],
                    [
                        'name' => $document->getClientOriginalName(),
                        'url' => $file->storeAs(
                            'agents',
                            $file->getMimeType() === 'application/octet-stream'
                                ? $file->hashNameWithExtension('pdf')
                                : $file->hashName(),
                            'public'
                        ),
                    ]
                );
                $updatedDocuments->push($key);
            }
        }

        if (Input::get('approve')) {
            if ($agent->user->hasRole('agent')) {
                tap($agent->user)
                ->update($request->validated());

                return back()->with('success', 'Updated agent');
            } else {
                tap($agent->user)
                    ->update($request->validated())
                    ->removeRole('new-agent')
                    ->assignRole('agent');

                $agent->update($request->validated());

                Mail::send('emails.agents.approved', [], function ($m) use ($agent) {
                    $m->from(config('mail.from.address'));
                    $m->to($agent->user->email)->subject('Your account has been approved');
                });

                return back()->with('success', 'Agent approved');
            }
        }

        $agent->fill($request->validated());

        if (!$agent->isDirty() && !$request->files->has('documents')) {
            return back()->with('success', 'Updated documents');
        }

        if ($request->user()->hasRole('new-agent')) {
            $agent->save();
            Mail::to(config('app.to.address'))->send(new AgentMail(
                $request->user(),
                'emails.agents.updated',
                $request->user()->getFullName() . ' has updated their profile',
                $updatedDocuments
            ));
            return back()->with('success', 'Updated documents');
        }

        if ($request->user()->hasRole('agent')) {

            Mail::to(config('app.to.address'))->send(new AgentMail(
                $request->user(),
                'emails.agents.updated',
                $request->user()->getFullName() . ' has updated their profile',
                $updatedDocuments
            ));

            tap($agent->user)
                ->removeRole('agent')
                ->assignRole('new-agent');

            $agent->save();

            return back()->with('success', 'Updated documents');
        }

        $agent->save();

        if ($agent->user->hasRole('new-agent')) {


            return back()->with('success', 'Updated Documents');
        }

        return back()->with('success', 'Updated documents');
    }
}
