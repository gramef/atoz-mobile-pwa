<?php

namespace App\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Bootstrap\RegisterProviders;
use Illuminate\Foundation\PackageManifest;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DebugRegisterProviders extends RegisterProviders
{
    protected $app;

    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        $this->app = $app;

        $this->registerConfiguredProviders();
    }

    /**
     * Register the configured service providers.
     *
     * @return void
     */
    public function registerConfiguredProviders()
    {
        $providers = Collection::make($this->app['config']['app.providers'])
            ->partition(function ($provider) {
                return Str::startsWith($provider, 'Illuminate\\');
            });

        $providers->splice(1, 0, [$this->app->make(PackageManifest::class)->providers()]);

        $collapsed = $providers->collapse()->toArray();

        echo "<!-- DEBUG: Starting Manual Provider Registration. Total: " . count($collapsed) . " -->\n";

        foreach ($collapsed as $provider) {
            if (!class_exists($provider)) {
                continue;
            }
            $this->app->register($provider);
        }
    }
}
