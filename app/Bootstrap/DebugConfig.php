<?php

namespace App\Bootstrap;

use Illuminate\Contracts\Foundation\Application;

class DebugConfig
{
    public function bootstrap(Application $app)
    {
        $app['config']->set('app.debug', true);
        $providers = $app['config']['app.providers'];
        if (is_array($providers)) {
            echo "<!-- DEBUG: Config Providers Count: " . count($providers) . " -->\n";
            // echo "<!-- DEBUG: First Provider: " . $providers[0] . " -->\n";
        } else {
            echo "<!-- DEBUG: Config Providers is NOT found or NOT array -->\n";
        }
    }
}
