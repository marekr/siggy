<?php 

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;

class CheckForMaintenanceMode {

    /**
     * The application implementation.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;
    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

	public function siggyRedirect($ajax, $url)
	{
		if( $ajax )
		{
			return response()->json(['redirect' => ltrim($url,'/')]);
		}
		else
		{
            return redirect($url);
		}
	}

    public function handle($request, Closure $next)
    {
        if ( $this->app->isDownForMaintenance() &&
				$request->route()->getActionName() != 'App\Http\Controllers\MaintenanceController@getIndex' )
        {
            return $this->siggyRedirect($request->ajax(), 'maintenance');
        }

        return $next($request);
    }

}