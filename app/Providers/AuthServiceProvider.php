<?php

namespace App\Providers;

use App\Document;
use App\InterpreterJob;
use App\Policies\DocumentPolicy;
use App\Policies\InterpreterJobPolicy;
use App\Policies\TranslatorJobPolicy;
use App\TranslatorJob;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        InterpreterJob::class => InterpreterJobPolicy::class,
        TranslatorJob::class => TranslatorJobPolicy::class,
        Document::class => DocumentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
   
        $this->registerPolicies();

        //
    }
}
