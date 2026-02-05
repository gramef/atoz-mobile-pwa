<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\TranslatorJob;
use Illuminate\Http\Request;

class TranslatorJobController extends Controller
{
    /**
     * Get list of translator jobs for the authenticated agent
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

        $jobs = TranslatorJob::query()
            ->with(['toLanguage', 'fromLanguage'])
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
            ->orderBy('target_date', 'desc')
            ->paginate($perPage > 0 ? $perPage : 20);

        return response()->json($jobs);
    }

    /**
     * Get a specific translator job
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

        $job = TranslatorJob::with([
            'toLanguage',
            'fromLanguage',
            'client.user',
            'agent.user',
            'documents',
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

        if (!$matched || in_array($matched->status, ['cancelled', 'rejected'])) {
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
     * Accept a translator job
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

        $job = TranslatorJob::with('matchedAgents')->find($id);

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
            'data' => $job->fresh(['toLanguage', 'fromLanguage', 'client.user', 'agent.user', 'documents'])
        ]);
    }
}
