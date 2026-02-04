<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\InterpreterJob;
use Illuminate\Http\Request;

class InterpreterJobController extends Controller
{
    /**
     * Get list of interpreter jobs for the authenticated agent
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

        $jobs = InterpreterJob::where('matched_agent_id', $agent->id)
            ->with(['client', 'to_language', 'timesheet'])
            ->orderBy('date', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $jobs
        ]);
    }

    /**
     * Get a specific interpreter job
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

        $job = InterpreterJob::where('id', $id)
            ->where('matched_agent_id', $agent->id)
            ->with(['client', 'to_language', 'timesheet', 'documents', 'feedback'])
            ->first();

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $job
        ]);
    }

    /**
     * Accept an interpreter job
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function accept(Request $request, $id)
    {
        $user = $request->user();
        $agent = $user->agent;

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'User is not an agent'
            ], 403);
        }

        $job = InterpreterJob::where('id', $id)
            ->where('matched_agent_id', $agent->id)
            ->first();

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found'
            ], 404);
        }

        if ($job->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Job cannot be accepted in its current state'
            ], 400);
        }

        $job->status = 'accepted';
        $job->save();

        return response()->json([
            'success' => true,
            'message' => 'Job accepted successfully',
            'data' => $job
        ]);
    }

    /**
     * Complete an interpreter job
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function complete(Request $request, $id)
    {
        $user = $request->user();
        $agent = $user->agent;

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'User is not an agent'
            ], 403);
        }

        $job = InterpreterJob::where('id', $id)
            ->where('matched_agent_id', $agent->id)
            ->first();

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found'
            ], 404);
        }

        if (!$job->canBeCompleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Job cannot be completed yet'
            ], 400);
        }

        $job->status = 'completed';
        $job->save();

        return response()->json([
            'success' => true,
            'message' => 'Job completed successfully',
            'data' => $job
        ]);
    }
}
