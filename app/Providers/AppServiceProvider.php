<?php

namespace App\Providers;

use App\User;
use App\Agent;
use App\InterpreterJob;
use App\MatchedAgent;
use App\Observers\UserObserver;
use App\Observers\AgentObserver;
use App\Observers\InterpreterJobObserver;
use App\Observers\MatchedAgentObserver;
use App\Observers\TranslatorJobObserver;
use App\TranslatorJob;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(UserObserver::class);
        Agent::observe(AgentObserver::class);
        InterpreterJob::observe(InterpreterJobObserver::class);
        TranslatorJob::observe(TranslatorJobObserver::class);
        MatchedAgent::observe(MatchedAgentObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        UploadedFile::macro('hashNameWithExtension', function (string $extension, string $path = null) {
            if ($path) {
                $path = rtrim($path, '/').'/';
            }
    
            $hash = $this->hashName ?: $this->hashName = Str::random(40);
    
            return $path.$hash.".".$extension;
        });
    }
}
