<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Language;

class LanguageController extends Controller
{
    public function index()
    {
        (object) $languages = Language::pluck('name', 'id');

        return response()->json($languages);
    }
}
