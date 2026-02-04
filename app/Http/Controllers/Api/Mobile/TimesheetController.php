<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Timesheet;
use App\InterpreterJob;
use Illuminate\Http\Request;

class TimesheetController extends Controller
{
    /**
     * Get list of timesheets for the authenticated agent
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $agent = $user->agent;

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'User is not an agent'
            ], 403);
        }

        $timesheets = Timesheet::where('agent_id', $agent->id)
            ->with(['interpreterJob'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $timesheets
        ]);
    }

    /**
     * Get a specific timesheet
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $agent = $user->agent;

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'User is not an agent'
            ], 403);
        }

        $timesheet = Timesheet::where('id', $id)
            ->where('agent_id', $agent->id)
            ->with(['interpreterJob'])
            ->first();

        if (!$timesheet) {
            return response()->json([
                'success' => false,
                'message' => 'Timesheet not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $timesheet
        ]);
    }

    /**
     * Create a new timesheet
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $agent = $user->agent;

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'User is not an agent'
            ], 403);
        }

        $request->validate([
            'job_id' => 'required|exists:interpreter_jobs,id',
            'agent_start_time' => 'required',
            'agent_end_time' => 'required',
            'agent_duration_hours' => 'required|integer|min:0',
            'agent_duration_minutes' => 'required|integer|min:0|max:59',
        ]);

        // Verify the job belongs to this agent
        $job = InterpreterJob::where('id', $request->job_id)
            ->where('matched_agent_id', $agent->id)
            ->first();

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found or not assigned to you'
            ], 404);
        }

        // Check if timesheet already exists for this job
        $existingTimesheet = Timesheet::where('job_id', $request->job_id)
            ->where('agent_id', $agent->id)
            ->first();

        if ($existingTimesheet) {
            return response()->json([
                'success' => false,
                'message' => 'Timesheet already exists for this job'
            ], 400);
        }

        $timesheet = Timesheet::create([
            'agent_id' => $agent->id,
            'job_id' => $request->job_id,
            'status' => 'pending',
            'agent_start_time' => $request->agent_start_time,
            'agent_end_time' => $request->agent_end_time,
            'agent_duration_hours' => $request->agent_duration_hours,
            'agent_duration_minutes' => $request->agent_duration_minutes,
            'client_name' => $request->client_name,
            'client_phone' => $request->client_phone,
            'client_designation' => $request->client_designation,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Timesheet created successfully',
            'data' => $timesheet
        ], 201);
    }

    /**
     * Sign a timesheet
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function sign(Request $request, $id)
    {
        $user = $request->user();
        $agent = $user->agent;

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'User is not an agent'
            ], 403);
        }

        $timesheet = Timesheet::where('id', $id)
            ->where('agent_id', $agent->id)
            ->first();

        if (!$timesheet) {
            return response()->json([
                'success' => false,
                'message' => 'Timesheet not found'
            ], 404);
        }

        $request->validate([
            'agent_signature' => 'required|string',
        ]);

        $timesheet->agent_signature = $request->agent_signature;
        $timesheet->agent_status = 'signed';
        $timesheet->save();

        return response()->json([
            'success' => true,
            'message' => 'Timesheet signed successfully',
            'data' => $timesheet
        ]);
    }
}
