<?php 

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Auth extends Facade {

    /**
     * Get the binding in the IoC container
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'auth';
    }
}