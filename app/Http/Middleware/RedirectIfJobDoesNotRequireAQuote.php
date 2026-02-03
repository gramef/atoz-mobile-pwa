<?php

namespace App\Http\Middleware;

use Closure;

class RedirectIfJobDoesNotRequireAQuote
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
        if (!$request->route('interpreterJob')->requiresQuote()) {
            return redirect()->back()->withErrors(['This job does not require a quote']);
        }

        return $next($request);
    }
}
