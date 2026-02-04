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

        $jobs = TranslatorJob::where('matched_agent_id', $agent->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $jobs
        ]);
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

        $job = TranslatorJob::where('id', $id)
            ->where('matched_agent_id', $agent->id)
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

        $job = TranslatorJob::where('id', $id)
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
}
