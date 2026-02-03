<?php

namespace App\Http\Controllers;

use App\Client;
use App\Company;
use Illuminate\Http\Request;

class RejectedClientController extends Controller
{
    public function index(Request $request)
    {
        return view('clients.rejected.index', [
            'clients' => Client::where('rejected', true)
                ->with([
                    'user', 
                    'organisation.company'
                ])
                ->filter($request->only('company'))
                ->paginate(10),
            'companies' => Company::onlyTrashed()->pluck('name', 'id'),
        ]);
    }

    public function update(Client $client)
    {
        $client->update([ 'rejected' => true ]);

        return redirect()->route('clients.index')->with('success', 'Request rejected');
    }
}
