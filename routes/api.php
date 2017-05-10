<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');

// Publicly accessible routes
$api->version('v1', ['middleware' => 'api.auth'], function ($api) {
	$api->group(['prefix' => 'v1'], function($api){
		
		$api->get('/chainmaps', 'App\Http\Controllers\Api\v1\ChainmapsController@getList');
		$api->get('/chainmaps/{id}', 'App\Http\Controllers\Api\v1\ChainmapsController@getChainmap');
		
		$api->get('/systems/{id}', 'App\Http\Controllers\Api\v1\SystemsController@getSystem');
	});
});