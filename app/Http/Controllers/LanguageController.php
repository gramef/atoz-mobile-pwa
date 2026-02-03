<?php

namespace App\Http\Controllers;

use App\Language;
use App\Http\Requests\LanguageRequest;
use Illuminate\Support\Facades\Mail;

class LanguageController extends Controller
{
    public function index()
    {
        return view('languages.index', [
            'languages' => Language::orderBy('name')->paginate(20)
        ]);
    }

    public function create()
    {
        return view('languages.create');
    }

    public function store(LanguageRequest $request)
    {
        Language::create($request->validated());

        return redirect()->route('languages.index')->with('success', 'Language created');
    }

    public function edit(Language $language)
    {
        return view('languages.edit', [
            'language' => $language,
        ]);
    }

    public function update(LanguageRequest $request, Language $language)
    {
        $language->update($request->validated());

        return redirect()->route('languages.index')->with('success', 'Language updated');
    }

    public function destroy(Language $language)
    {
        $language->agents()->detach();
        $language->delete();

        return redirect()->route('languages.index')->with('success', 'Language deleted');
    }
}
