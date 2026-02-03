<?php

namespace App\Http\Controllers;
use App\InterpreterJob;
use App\TravelExpences;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class InterpreterJobDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(InterpreterJob $interpreterJob)
    {
        $travel = TravelExpences::where('job_id',$interpreterJob->id)->first();
        return view('interpreter-jobs.documents.index', [
            'interpreterJob' => $interpreterJob,'travel' => $travel,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, InterpreterJob $interpreterJob)
    {
        $request->validate([
            'file' => ['required', 'file'],
            'document_type' => ['required', Rule::in(config('enums.document_types'))],
        ]);

        $documentUrl = $request->file('file')->store("interpreter-jobs/$interpreterJob->id", 'public');
        $documentName = $request->file('file')->getClientOriginalName();

        $existingDocument = $interpreterJob->documents->firstWhere('type', $request->document_type);
        $isTranslatedFile = optional($existingDocument)->type == config('enums.document_types')['translated_file'];

        if ($existingDocument && !$isTranslatedFile) {
            Storage::disk('public')->delete($existingDocument->url);
        }

        if ($isTranslatedFile) {
            $interpreterJob->documents()->create([
                'type' => $request->document_type,
                'url' => $documentUrl,
                'name' => $documentName,
            ]);
        } else {
            $interpreterJob->documents()->updateOrCreate(
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
            'fullUrl' => Storage::disk('public')->url($documentUrl),
            'name' => $documentName,
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function travel_details(Request $request){


        $validatedData = $request->validate([
            'start_time' => 'required',
            'end_time' => 'required',
            'date' => 'required',
            'file' => 'required'
        ]);

        $travelEx = TravelExpences::where('job_id', $request['job_id'])->first();
    
        if (!$travelEx) {
            // If no travelEx exists, create a new one
            $travelEx = new TravelExpences();
            $travelEx->job_id = $request['job_id'];
            $travelEx->agent_id = $request['agent_id'];
        }
    
        // Update or set the travelEx attributes
        $travelEx->travel_start_time = $validatedData['start_time'];
        $travelEx->travel_end_time = $validatedData['end_time'];
        $travelEx->travel_amount = $request['amount'];
        $travelEx->travel_date = $validatedData['date'];
    
        // Save the travelEx (new or updated)
        $travelEx->save();
    
        return redirect()->back()->with('success', 'Travel Details Updated successfully.');
    }
}
