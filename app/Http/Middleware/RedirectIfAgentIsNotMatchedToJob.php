<?php

namespace App\Http\Middleware;

use Closure;

class RedirectIfAgentIsNotMatchedToJob
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
        $job = $request->route('interpreterJob') ?? $request->route('translatorJob');
        $notMatchedToJob = !optional($job->matchedLoggedInAgent())->exists();

        if ($request->user()->hasRole('agent') && $notMatchedToJob) {
            return redirect()->back()->withErrors(['You are not matched to this job']);
        }

        return $next($request);
    }
}
