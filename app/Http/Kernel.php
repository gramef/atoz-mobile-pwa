<?php

namespace App\Http;

use App\Http\Middleware\RedirectNewAgentToProfile;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use App\Http\Middleware\RedirectIfAgentHasNotQuotedJob;
use App\Http\Middleware\RedirectIfJobHasNoAssignedAgent;
use App\Http\Middleware\RedirectIfAgentIsNotMatchedToJob;
use App\Http\Middleware\RedirectIfJobDoesNotRequireAQuote;
use App\Http\Middleware\RedirectAgentIfJobIsAlreadyAssigned;
use App\Http\Middleware\RedirectIfNoAgentsHaveQuotedJob;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */

    protected $middleware = [
        \App\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\Impersonate::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
            \Barryvdh\Cors\HandleCors::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
         'blockIP' =>  \App\Http\Middleware\BlockIpMiddleware::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
        'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
        'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,
        'redirect_if_agent_has_not_quoted_job' => RedirectIfAgentHasNotQuotedJob::class,
        'redirect_agent_if_job_is_already_assigned' => RedirectAgentIfJobIsAlreadyAssigned::class,
        'redirect_if_job_does_not_require_quote' => RedirectIfJobDoesNotRequireAQuote::class,
        'redirect_if_job_has_no_assigned_agent' => RedirectIfJobHasNoAssignedAgent::class,
        'redirect_if_agent_is_not_matched_to_job' => RedirectIfAgentIsNotMatchedToJob::class,
        'redirect_new_agent_to_profile' => RedirectNewAgentToProfile::class,
        'redirect_if_no_agents_have_quoted_job' => RedirectIfNoAgentsHaveQuotedJob::class,
        
    ];

    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority = [
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\Authenticate::class,
        \Illuminate\Session\Middleware\AuthenticateSession::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,
    ];
}
