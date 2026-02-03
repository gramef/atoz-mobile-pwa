<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function index(Request $request)
    {
        (object) $skills = Skill::where('type', $request->get('type', '0'))->pluck('skill', 'id');

        return response()->json($skills);
    }
}
