<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Siggy\UserSession;

class SiggySessionServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('siggysession', function ($app) {
            return new UserSession();
        });
    }
}