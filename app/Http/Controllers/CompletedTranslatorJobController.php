<?php

namespace App\Http\Controllers;

use App\TranslatorJob;

class CompletedTranslatorJobController extends Controller
{
    public function update(TranslatorJob $translatorJob)
    {
        $translatorJob->update(['status' => 4]);
        return back()->with('success', 'Completed job');
    }
}
