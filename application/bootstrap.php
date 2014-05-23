<?php defined('SYSPATH') or die('No direct script access.');

define('SIGGY_VERSION', '2.9.1');
define('WIN_DEV', true);
define('NEWFRONT', true);
define('CACHE_METHOD', 'default');
// -- Environment setup --------------------------------------------------------

// Load the core Kohana class
require SYSPATH.'classes/kohana/core'.EXT;

if (is_file(APPPATH.'classes/kohana'.EXT))
{
	// Application extends the core
	require APPPATH.'classes/kohana'.EXT;
}
else
{
	// Load empty core extension
	require SYSPATH.'classes/kohana'.EXT;
}


/**
 * Set the default time zone.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/timezones
 */
date_default_timezone_set('UTC');

/**
 * Set the default locale.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/setlocale
 */
setlocale(LC_ALL, 'en_US.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @see  http://kohanaframework.org/guide/using.autoloading
 * @see  http://php.net/spl_autoload_register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @see  http://php.net/spl_autoload_call
 * @see  http://php.net/manual/var.configuration.php#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

// -- Configuration and initialization -----------------------------------------

/**
 * Set the default language
 */
I18n::lang('en-us');

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */
//if (isset($_SERVER['KOHANA_ENV']))
//{
//	Kohana::$environment = constant('Kohana::'.strtoupper($_SERVER['KOHANA_ENV']));
//}

		Cookie::$salt = 'y[$e.swbDs@|Gd(ndtUSy^';
/**
 * FUCK the above code, too much work.
 * Set the environment string by the domain (defaults to 'development').
 */


$headers = apache_request_headers();
foreach($headers as $k => $v)
{
	if( strpos($k,'EVE') == 0 )
	{
		$_SERVER['HTTP_' . $k] = $v;
	}
}
 
Kohana::$environment = ($_SERVER['SERVER_NAME'] !== 'localhost') ? Kohana::PRODUCTION : Kohana::DEVELOPMENT;


if( Kohana::$environment == Kohana::DEVELOPMENT && strpos($_SERVER['HTTP_USER_AGENT'],'EVE-IGB') === false )
{
  $_SERVER['HTTP_EVE_CORPID'] = 389326446;
  $_SERVER['HTTP_EVE_CHARID'] = 460256976;
  $_SERVER['HTTP_EVE_CHARNAME'] = 'Messoroz ROCKS';
  $_SERVER['HTTP_EVE_TRUSTED'] = 'Yes';
  //$_SERVER['HTTP_EVE_TRUSTED'] = 'No';
  $_SERVER['HTTP_USER_AGENT'] = 'Boo EVE-IGB';
  $_SERVER['HTTP_EVE_SHIPTYPEID'] = 1252;
  $lol = false;
	if( $lol )
	{
		$_SERVER['HTTP_EVE_SOLARSYSTEMNAME'] = 'Fricoure';
		$_SERVER['HTTP_EVE_SOLARSYSTEMID'] = 30002734;
	}
	else
	{
  $_SERVER['HTTP_EVE_SOLARSYSTEMNAME'] = 'J100549';
  $_SERVER['HTTP_EVE_SOLARSYSTEMID'] = 31002019;
	}
}



/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 */
Kohana::init(array(
  'base_url'   => 'http://localhost/evetel',
  'index_file' => FALSE,  
  'profile'    => Kohana::$environment !== Kohana::PRODUCTION,
  'caching'    => Kohana::$environment === Kohana::PRODUCTION,	
  'errors' => ( Kohana::$environment === Kohana::PRODUCTION ? FALSE : TRUE )
));


/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File(APPPATH.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
	// 'auth'       => MODPATH.'auth',       // Basic authentication
	 'cache'      => MODPATH.'cache',      // Caching with multiple backends
	// 'codebench'  => MODPATH.'codebench',  // Benchmarking tool
	 'database'   => MODPATH.'database',   // Database access
	// 'image'      => MODPATH.'image',      // Image manipulation
	 'orm'        => MODPATH.'orm',        // Object Relationship Mapping
	// 'unittest'   => MODPATH.'unittest',   // Unit testing
	// 'userguide'  => MODPATH.'userguide',  // User guide and API documentation
	));

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */
//if ( ! Route::cache())
//{
   Route::set('siggyUpdate', 'update')
    ->defaults(array(
        'controller' => 'siggy',
        'action' => 'update',
    )); 
   Route::set('siggyUpdateSilent', 'updateSilent')
    ->defaults(array(
        'controller' => 'siggy',
        'action' => 'updateSilent',
    )); 
   Route::set('siggyGetJumpLog', 'getJumpLog')
    ->defaults(array(
        'controller' => 'siggy',
        'action' => 'getJumpLog',
    )); 
    /*
   Route::set('siggySystemData', 'getSystemData(/<name>)')
    ->defaults(array(
        'controller' => 'siggy',
        'action' => 'systemData',
    )); */
   Route::set('siggyPreProcess', 'preProcess(/<start>)')
    ->defaults(array(
        'controller' => 'special',
        'action' => 'preProcess',
    )); 
   Route::set('siggyFixGroup', 'fixGroup')
    ->defaults(array(
        'controller' => 'special',
        'action' => 'fixGroup',
    )); 
   Route::set('siggyProcessStatics', 'processStatics(/<region>)')
    ->defaults(array(
        'controller' => 'special',
        'action' => 'processStatics',
    )); 
    
   Route::set('siggyBuildStatic', 'buildStatics')
    ->defaults(array(
        'controller' => 'special',
        'action' => 'buildStatics',
    )); 
    

   Route::set('siggySigs', 'do(<action>)')
    ->defaults(array(
        'controller' => 'siggy'
    ));     
    

   Route::set('siggy-process', 'process/<action>')
    ->defaults(array(
        'controller' => 'siggy'
    ));     

   Route::set('login', 'manage/login')
    ->defaults(array(
        'controller' => 'user',
        'action' => 'login',
    )); 
    
   Route::set('manage', 'manage(/<controller>(/<action>(/<id>)))')
    ->defaults(array(
				'directory'  => 'manage',
        'controller' => 'group',
        'action' => 'index',
    )); 
    
    
    Route::set('account', 'account(/<action>)')
    ->defaults(array(
        'controller' => 'account',
        'action' => 'login'
    )); 
    
    Route::set('cron', 'cron(/<action>)')
    ->defaults(array(
        'controller' => 'cron'
    )); 
    
    Route::set('apiChainMaps', 'api/chainMap(/<action>)')
    ->defaults(array(
        'controller' => 'api'
    )); 
    
    
   Route::set('siggyMain', 'system(/<name>)')
    ->defaults(array(
        'controller' => 'siggy',
        'action' => 'index',
    )); 

   Route::set('stats', 'stats(/year/<year>(/month(/<month>))(/week/<week>))')
    ->defaults(array(
        'controller' => 'stats',
        'action' => 'index',
    ));     
    

  Route::set('pages', 'pages(/<page>)')
    ->defaults(array(
      'controller' => 'pages',
      'action'     => 'viewPage',
    ));

  Route::set('default', '(<controller>(/<action>(/<id>)))')
    ->defaults(array(
      'controller' => 'siggy',
      'action'     => 'index',
    ));
    
  //  if( Kohana::$environment !== Kohana::PRODUCTION )
   // {
    //  Route::cache(TRUE);
   // }
//}