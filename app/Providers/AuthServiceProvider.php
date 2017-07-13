<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Laravel\Passport\Passport;
use Siggy\AuthManager;

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
            return new AuthManager();
        });
    }
	
    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        Passport::routes();
    }
}