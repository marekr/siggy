<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [
    'uses' => 'SiggyController@index', 
    'as' => 'siggy'
]);

Route::get('/system/{system}', [
    'uses' => 'SiggyController@index', 
    'as' => 'siggy'
]);

Route::get('manage', [
    'uses' => 'ManageController@admin',
    'as' => 'manage',
    'middleware' => 'admin'
]);