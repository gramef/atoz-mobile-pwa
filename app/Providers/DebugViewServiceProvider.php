<?php

namespace App\Providers;

use Illuminate\View\ViewServiceProvider;

class DebugViewServiceProvider extends ViewServiceProvider
{
    public function register()
    {
        error_log("DEBUG: DebugViewServiceProvider REGISTER called!");
        echo "<!-- DEBUG: DebugViewServiceProvider REGISTER -->\n";

        parent::register();
    }

    public function boot()
    {
        error_log("DEBUG: DebugViewServiceProvider BOOT called!");
        // parent::boot(); // ViewServiceProvider doesn't have boot
    }
}
