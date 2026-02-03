<?php

namespace App\Http\Controllers;

use App\TranslatorJob;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;


class TranslatorJobDocumentController extends Controller
{
    public function index(TranslatorJob $translatorJob)
    {
        return view('translator-jobs.documents.index', [
            'translatorJob' => $translatorJob->load('comments.user'),
        ]);
    }

    public function store(Request $request, TranslatorJob $translatorJob)
    {
        $request->validate([
            'file' => ['required', 'file'],
            'document_type' => ['required', Rule::in(config('enums.document_types'))],
        ]);

        $documentUrl = $request->file('file')->store("translator-jobs/$translatorJob->id", 's3');
        $documentName = $request->file('file')->getClientOriginalName();

        $existingDocument = $translatorJob->documents->firstWhere('type', $request->document_type);
        $isTranslatedFile = optional($existingDocument)->type == config('enums.document_types')['translated_file'];

        if ($existingDocument && !$isTranslatedFile) {
            Storage::disk('s3')->delete($existingDocument->url);
        }

        if ($isTranslatedFile) {
            $translatorJob->documents()->create([
                'type' => $request->document_type,
                'url' => $documentUrl,
                'name' => $documentName,
            ]);
        } else {
            $translatorJob->documents()->updateOrCreate(
                [
                    'type' => $request->document_type
                ],
                [
                    'url' => $documentUrl,
                    'name' => $documentName,
                ]
            );
        }

        return response()->json([
            'fullUrl' => Storage::disk('s3')->url($documentUrl),
            'name' => $documentName,
        ]);
    }
}
