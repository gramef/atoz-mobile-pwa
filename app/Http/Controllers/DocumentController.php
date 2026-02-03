<?php

namespace App\Http\Controllers;

use App\Document;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function destroy(Document $document)
    {
        if (Storage::disk('public')->has($document->url)) {
            Storage::disk('public')->delete($document->url);
        }

        $document->delete();
    }
}
