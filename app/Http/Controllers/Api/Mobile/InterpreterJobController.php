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

        $perPage = (int) $request->get('per_page', 20);

        $jobs = InterpreterJob::query()
            ->with(['to_language', 'client', 'timesheet'])
            ->where(function ($q) use ($agent) {
                $q->where('agent_id', $agent->id)
                    ->orWhere(function ($q) use ($agent) {
                        $q->whereNull('agent_id')
                            ->whereHas('matchedAgents', function ($mq) use ($agent) {
                                $mq->where('agent_id', $agent->id)
                                    ->whereIn('status', [0, 2]);
                            });
                    });
            })
            ->orderBy('appointment_date', 'desc')
            ->paginate($perPage > 0 ? $perPage : 20);

        return response()->json($jobs);
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

        $job = InterpreterJob::with([
            'to_language',
            'client.user',
            'agent.user',
            'timesheet',
            'documents',
            'feedback',
        ])->find($id);

        if (!$job) {
            return response()->json([
                'message' => 'Job not found'
            ], 404);
        }

        if ((int) $job->agent_id === (int) $agent->id) {
            return response()->json($job);
        }

        $matched = $job->matchedAgents()->where('agent_id', $agent->id)->first();

        if (!$matched) {
            return response()->json([
                'message' => 'Job not found'
            ], 404);
        }

        if (in_array($matched->status, ['cancelled', 'rejected'])) {
            return response()->json([
                'message' => 'Job not found'
            ], 404);
        }

        if ($job->agent_id && (int) $job->agent_id !== (int) $agent->id) {
            return response()->json([
                'message' => 'This assignment is no longer available!'
            ], 403);
        }

        return response()->json($job);
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

        $job = InterpreterJob::with('matchedAgents')->find($id);

        if (!$job) {
            return response()->json([
                'message' => 'Job not found'
            ], 404);
        }

        if ($job->agent_id && (int) $job->agent_id !== (int) $agent->id) {
            return response()->json([
                'message' => 'This assignment is no longer available!'
            ], 403);
        }

        $matched = $job->matchedAgents()->where('agent_id', $agent->id)->first();

        if (!$matched) {
            return response()->json([
                'message' => 'Job not found'
            ], 404);
        }

        if (in_array($matched->status, ['cancelled', 'rejected'])) {
            return response()->json([
                'message' => 'Job not found'
            ], 404);
        }

        if ((int) $job->status !== 0 && (int) $job->status !== 5) {
            return response()->json([
                'message' => 'Job cannot be accepted in its current state'
            ], 400);
        }

        $matched->update(['status' => 4]);
        $job->update([
            'agent_id' => $agent->id,
            'status' => 1,
        ]);

        return response()->json([
            'message' => 'Job accepted successfully',
            'data' => $job->fresh(['to_language', 'client.user', 'agent.user', 'timesheet', 'documents', 'feedback'])
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

        $job = InterpreterJob::find($id);

        if (!$job) {
            return response()->json([
                'message' => 'Job not found'
            ], 404);
        }

        if ((int) $job->agent_id !== (int) $agent->id) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        if (!$job->canBeCompleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Job cannot be completed yet'
            ], 400);
        }

        $job->update(['status' => 4]);

        return response()->json([
            'message' => 'Job completed successfully',
            'data' => $job->fresh(['to_language', 'client.user', 'agent.user', 'timesheet', 'documents', 'feedback'])
        ]);
    }
}
