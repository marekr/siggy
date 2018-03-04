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

Route::get('/maintenance', [
    'uses' => 'MaintenanceController@getIndex', 
    'as' => 'maintenance'
]);

Route::get('/pages/welcome', [
    'uses' => 'PageController@welcome', 
    'as' => 'welcome'
]);

Route::get('/pages/about', [
    'uses' => 'PageController@about', 
    'as' => 'about'
]);

Route::get('/pages/costs', [
    'uses' => 'PageController@costs', 
    'as' => 'costs'
]);

Route::get('/changelog', [
    'uses' => 'ChangelogController@index', 
    'as' => 'changelog'
]);

Route::get('/account/login', [
    'uses' => 'AccountController@getLogin', 
    'as' => 'login'
]);

Route::post('/account/login', [
    'uses' => 'AccountController@postLogin', 
    'as' => 'login',
	'middleware' => ['csrf']
]);

Route::get('/account/sso/complete', 'AccountController@getSSOComplete');

Route::get('/account/sso/{id}', [
    'uses' => 'AccountController@sso', 
    'as' => 'sso'
]);

Route::post('/account/sso/{id}', [
    'uses' => 'AccountController@sso', 
    'as' => 'sso'
]);

Route::get('/account/register', [
    'uses' => 'AccountController@getRegister', 
    'as' => 'register'
]);

Route::post('/account/register', [
    'uses' => 'AccountController@postRegister', 
    'as' => 'register',
	'middleware' => ['csrf']
]);


Route::post('/account/password_reset', 
	[
		'uses' => 'AccountController@postForgotPassword',
		'middleware' => ['csrf']
	]
);


Route::get('/account/password_reset', [
	'uses' => 'AccountController@getForgotPassword', 
	'as' => 'forgotPassword'
]);


Route::post('/account/password_reset/{token}', 'AccountController@postCompletePasswordReset');

Route::get('/account/password_reset/{token}', [
	'uses' => 'AccountController@getCompletePasswordReset', 
	'as' => 'completePasswordReset'
]);

Route::get('/announcements', [
	'uses' => 'AnnouncementController@list', 
	'as' => 'announcements'
]);

Route::get('/announcements/view/{id}', 'AnnouncementController@view');


Route::get('prometheus','PrometheusController@index');

Route::group(['middleware' => ['web','siggy.app']], function () {
	
	Route::get('/', [
		'uses' => 'SiggyController@index', 
		'as' => 'siggy'
	]);
	Route::get('/data/systems','DataController@systems');
	Route::get('/data/sig_types','DataController@sigTypes');
	Route::get('/data/structures','DataController@structures');
	Route::get('/data/poses','DataController@poses');
	Route::get('/data/ships','DataController@ships');
	Route::get('/data/effects','DataController@effects');
	Route::get('/data/locale/{locale}','DataController@locale');
	Route::post('/update','SiggyController@update');
	Route::post('siggy/siggy','SiggyController@siggy');
	Route::post('siggy/save_system','SiggyController@saveSystem');
	Route::post('siggy/notes_save','SiggyController@notesSave');
	Route::post('siggy/save_character_settings','SiggyController@saveCharacterSettings');

	Route::post('crest/waypoint','CrestController@waypoint');

	Route::post('sig/add','SignatureController@add');
	Route::post('sig/edit','SignatureController@edit');
	Route::post('sig/remove','SignatureController@remove');
	Route::post('sig/mass_add','SignatureController@mass_add');
	Route::get('sig/scanned_systems','SignatureController@scanned_systems');

	Route::post('pos/add','POSController@add');
	Route::post('pos/edit','POSController@edit');
	Route::post('pos/remove','POSController@remove');

	Route::post('structure/add','StructureController@add');
	Route::post('structure/edit','StructureController@edit');
	Route::post('structure/remove','StructureController@remove');
	Route::post('structure/vulnerabilities','StructureController@vulnerabilities');

	Route::post('dscan/add','DScanController@add');
	Route::post('dscan/remove','DScanController@remove');
	Route::get('dscan/json/{id}','DScanController@view');

	Route::get('chainmap/connections','ChainmapController@connections');
	Route::post('chainmap/connection_delete','ChainmapController@connection_delete');
	Route::post('chainmap/save','ChainmapController@save');
	Route::post('chainmap/connection_edit','ChainmapController@connection_edit');
	Route::post('chainmap/find_nearest_exits','ChainmapController@find_nearest_exits');
	Route::post('chainmap/connection_add','ChainmapController@connection_add');
	Route::post('chainmap/switch','ChainmapController@switch');
	Route::get('chainmap/autocomplete_wh','ChainmapController@autocomplete_wh');
	Route::get('chainmap/jump_log','ChainmapController@jump_log');

	Route::get('thera/latest_exits','TheraController@latest_exits');
	Route::post('thera/import_to_chainmap','TheraController@import_to_chainmap');

	Route::get('search/everything','SearchController@everything');

	Route::get('stats','StatsController@overview');
	Route::get('stats/overview','StatsController@overview');
	Route::get('stats/leaderboard','StatsController@leaderboard');

	Route::get('stats/pos_adds/','StatsController@posAdds');
	Route::get('stats/pos_updates/','StatsController@posUpdates');
	Route::get('stats/wormholes/','StatsController@wormholes');
	Route::get('stats/sig_adds/','StatsController@sigAdds');
	Route::get('stats/sig_updates/','StatsController@sigUpdates');

	Route::get('stats/overview','StatsController@overview');

	Route::post('notifications/read','NotificationController@read');
	Route::get('notifications/all','NotificationController@all');
	Route::get('notifications/notifiers','NotificationController@notifiers');
	Route::post('notifications/notifiers_add','NotificationController@notifiers_add');
	Route::post('notifications/notifiers_delete','NotificationController@notifiers_delete');


	Route::get('access/group_password','AccessController@getGroupPassword');
	Route::post('access/group_password','AccessController@postGroupPassword');
});


Route::group(['middleware' => ['siggy.app-pages']], function () {

	Route::get('/backend/esi', [
		'uses' => 'BackendController@esi', 
		'as' => 'backendesi'
	]);

	Route::get('/account/logout', [
		'uses' => 'AccountController@getLogout', 
		'as' => 'logout'
	]);

	Route::get('/account/characters', [
		'uses' => 'AccountController@getCharacters', 
		'as' => 'characters'
	]);

	Route::post('/account/characters', 'AccountController@postCharacters');

	Route::get('/account/connected', [
		'uses' => 'AccountController@getConnected', 
		'as' => 'connected'
	]);

	Route::get('/account/connect', 'AccountController@getConnect');

	Route::post('/account/disconnect', 'AccountController@postDisconnect');

	Route::get('/account', 'AccountController@getOverview');
	Route::get('/account/overview', 'AccountController@getOverview');
	
	Route::get('/account/changePassword', [
		'uses' => 'AccountController@getChangePassword', 
		'as' => 'changePassword'
	]);

	Route::post('/account/changePassword', 'AccountController@postChangePassword');
	
	Route::get('/group/create', [
		'uses' => 'GroupController@getCreateIntro'
	]);
	
	Route::get('/group/create/intro', [
		'uses' => 'GroupController@getCreateIntro', 
		'as' => 'createIntro'
	]);

	Route::get('/group/create/form', [
		'uses' => 'GroupController@getCreateForm', 
		'as' => 'createForm'
	]);
	Route::post('/group/create/form', 'GroupController@postCreateForm');

	
	Route::get('/group/create/completed', [
		'uses' => 'GroupController@getCreateCompleted', 
		'as' => 'createCompleted'
	]);
	

	Route::get('access/groups','AccessController@getGroups');
	Route::post('access/groups','AccessController@postGroups');
	Route::get('access/blacklisted','AccessController@getBlacklisted');
});

Route::group(['namespace' => 'Manage','prefix' => 'manage','middleware' => ['siggy.manage']], function()
{
	Route::get('/', [
		'uses' => 'DashboardController@index',
		'as' => 'manage'
	]);


	Route::get('dashboard','DashboardController@index');

	Route::get('settings/general','SettingsController@getGeneral');
	Route::post('settings/general','SettingsController@postGeneral');
	
	Route::get('settings/chainmap','SettingsController@getChainmap');
	Route::post('settings/chainmap','SettingsController@postChainmap');
	
	Route::get('settings/statistics','SettingsController@getStatistics');
	Route::post('settings/statistics','SettingsController@postStatistics');
	
	Route::get('blacklist/list','BlacklistController@getList');
	Route::get('blacklist/add','BlacklistController@getAdd');
	Route::post('blacklist/add','BlacklistController@postAdd');
	Route::get('blacklist/remove/{id}','BlacklistController@getRemove');
	
	Route::get('apikeys/list','ApiKeyController@getList');
	Route::get('apikeys/add','ApiKeyController@getAdd');
	Route::post('apikeys/add','ApiKeyController@postAdd');
	Route::post('apikeys/remove/{id}','ApiKeyController@postRemove');

	Route::get('logs/activity','LogsController@getActivity');
	Route::get('logs/characters','LogsController@getCharacters');
	
	Route::get('billing/overview','BillingController@getOverview');

		
	Route::get('access/configure','AccessController@getConfigure');
	Route::get('access/add','AccessController@getAdd');
	Route::post('access/add','AccessController@postAdd');
	
	Route::get('access/edit/{id}','AccessController@getEdit');
	Route::post('access/edit/{id}','AccessController@postEdit');
	
	Route::get('access/remove/{id}','AccessController@getRemove');
	
	Route::get('chainmaps/list','ChainmapsController@getList');
	Route::get('chainmaps/add','ChainmapsController@getAdd');
	Route::post('chainmaps/add','ChainmapsController@postAdd');

	Route::get('chainmaps/edit/{id}','ChainmapsController@getEdit');
	Route::post('chainmaps/edit/{id}','ChainmapsController@postEdit');
	
	Route::get('chainmaps/remove/{id}','ChainmapsController@getRemove');
	Route::post('chainmaps/remove/{id}','ChainmapsController@postRemove');
	
	Route::get('chainmaps/access/remove/{id}','ChainmapsController@getRemoveAccess');
	Route::post('chainmaps/access/remove/{id}','ChainmapsController@postRemoveAccess');

	Route::get('group/members','GroupMembersController@getList');
	Route::get('group/members/add','GroupMembersController@getAdd');
	Route::post('group/members/add','GroupMembersController@postAdd');
	Route::post('group/members/add/details','GroupMembersController@postAddDetails');
	Route::post('group/members/add/finish','GroupMembersController@postAddFinish');

	
	Route::post('access/set','AccessController@postSet');
});


Route::group(['middleware' => ['web','siggy.app']], function () {
	
	Route::get('{jsroute}', [
		'uses' => 'SiggyController@index'
	])->where('jsroute', '.*');

});

