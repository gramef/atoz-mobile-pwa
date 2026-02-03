<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BlockIpMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // List of allowed IP addresses
        $allowedIps = [
            '78.146.63.98', // Replace with your IP address
            '111.222.333.444', // Replace with your friend's IP address
        ];

        $clientIp = $request->ip();
        Log::info('Client IP: ' . $clientIp);

        if (!in_array($clientIp, $allowedIps)) {
            return response()->json(['message' => 'Your IP is not allowed.'], 403);
        }

        return $next($request);
    }
}


?>