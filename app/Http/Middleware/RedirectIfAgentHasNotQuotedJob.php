<?php

namespace App\Http\Middleware;

use Closure;

class RedirectIfAgentHasNotQuotedJob
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user()->hasRole('agent')) {
            $job = $request->route('interpreterJob') ?? $request->route('translatorJob');

            $agentHasNotQuotedForJob = $job->matchedLoggedInAgent()->quotes->isEmpty();
            $agentHasNotQuotedForInterpreterJob = $request->route('interpreterJob') && $job->client->always_requires_a_quote && $agentHasNotQuotedForJob;
            $agentHasNotQuotedForTranslatorJob = $request->route('translatorJob') && $agentHasNotQuotedForJob;

            if ($agentHasNotQuotedForInterpreterJob || $agentHasNotQuotedForTranslatorJob) {
                return redirect()->back()->withErrors(['You must provide a quote for this job before accepting it']);
            }
        }

        return $next($request);
    }
}
