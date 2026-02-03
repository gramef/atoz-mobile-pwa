<?php

namespace App\Http\Middleware;

use Closure;

class RedirectAgentIfJobIsAlreadyAssigned
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

        if ($request->user()->hasRole('agent') && $job->assignedMatched()->exists()) {
            return redirect()->back()->withErrors(['This job has already been assigned']);
        }

        return $next($request);
    }
}
