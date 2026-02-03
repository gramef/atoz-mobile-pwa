<?php

namespace App\Http\Middleware;

use Closure;

class RedirectNewAgentToProfile
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
        if ($request->user()->hasRole('new-agent')) {
            return redirect()->route('agents.profile.create');
        }

        return $next($request);
    }
}
