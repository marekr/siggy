<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Siggy\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('auth', function ($app) {
            return new Auth();
        });
    }
}