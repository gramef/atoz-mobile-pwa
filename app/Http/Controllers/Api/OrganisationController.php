<?php

namespace App\Http\Controllers\Api;

use App\Organisation;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class OrganisationController extends Controller
{
    public function show(Request $request)
    {
        if($request->id) {
            return response()->json(Organisation::where('company_id', $request->id)->first());
        }
    }

}
