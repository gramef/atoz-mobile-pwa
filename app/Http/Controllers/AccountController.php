<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function create(Request $request, $token = null)
    {
        return view('auth.account.setup', [
            'token' => $token,
        ]);
    }
}
