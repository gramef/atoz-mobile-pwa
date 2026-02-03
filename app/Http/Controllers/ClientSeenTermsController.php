<?php

namespace App\Http\Controllers;

use App\Client;

class ClientSeenTermsController extends Controller
{
    public function update(Client $client)
    {
        $client->update(['seen_terms' => true]);
    }
}
