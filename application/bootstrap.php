<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Fixup $_SERVER headers
 * Need to strtoupper and replace dashes with udnerscores for eve headers
 * as apache->fastcgi converts it
 */
$headers = apache_request_headers();
foreach($headers as $k => $v)
{
	if( strpos($k,'EVE') == 0 )
	{
		$k = 'HTTP_' . strtoupper(str_replace('-','_',$k));
		if( !isset($_SERVER[$k]) )
			$_SERVER[$k] = $v;
	}
}

//CCP putting xml tags in header fix
if( isset($_SERVER['HTTP_EVE_SHIPTYPENAME']) )
{
	$_SERVER['HTTP_EVE_SHIPTYPENAME'] = str_replace('*','',strip_tags($_SERVER['HTTP_EVE_SHIPTYPENAME']));
}

if( isset($_SERVER['HTTP_EVE_REGIONNAME']) )
{
	$_SERVER['HTTP_EVE_REGIONNAME'] = str_replace('*','',strip_tags($_SERVER['HTTP_EVE_REGIONNAME']));
}

if( isset($_SERVER['HTTP_EVE_SOLARSYSTEMNAME']) )
{
	$_SERVER['HTTP_EVE_SOLARSYSTEMNAME'] = str_replace('*','',strip_tags($_SERVER['HTTP_EVE_SOLARSYSTEMNAME']));
}

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

/**
 * Set the mb_substitute_character to "none"
 *
 * @link http://www.php.net/manual/function.mb-substitute-character.php
 */
mb_substitute_character('none');
// -- Configuration and initialization -----------------------------------------

/**
 * Set the default language
 */
I18n::lang('en-us');

if (isset($_SERVER['SERVER_PROTOCOL']))
{
	// Replace the default protocol.
	HTTP::$protocol = $_SERVER['SERVER_PROTOCOL'];
}

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */

Kohana::$environment = ($_SERVER['SERVER_NAME'] !== 'localhost' && $_SERVER['SERVER_NAME'] !== 'dev.siggy.borkedlabs.com') ? Kohana::PRODUCTION : Kohana::DEVELOPMENT;

define('SIGGY_VERSION', '2.27.0e');
if( Kohana::$environment == Kohana::PRODUCTION)
{
	define('WIN_DEV', true);
	define('CACHE_METHOD', 'memcache');
}
else
{
	define('WIN_DEV', true);
	define('CACHE_METHOD', 'default');
}



Cookie::$salt = 'y[$e.swbDs@|Gd(ndtUSy^';
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
$initOptions = array(
  'base_url'   => 'http://siggy.borkedlabs.com',
  'index_file' => FALSE,
  'profile'    => Kohana::$environment !== Kohana::PRODUCTION,
  'caching'    => Kohana::$environment === Kohana::PRODUCTION,
  //'errors' => ( Kohana::$environment === Kohana::PRODUCTION ? FALSE : TRUE )
  'errors' => TRUE
);

if( Kohana::$environment == Kohana::PRODUCTION)
{
	$initOptions['base_url'] = 'http://siggy.borkedlabs.com';
}
else
{
	$initOptions['base_url'] = 'http://dev.siggy.borkedlabs.com';
}

Kohana::init($initOptions);

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
	 'cache'      => MODPATH.'cache',      // Caching with multiple backends
	 'database'   => MODPATH.'database',   // Database access
	 'restful_api' => MODPATH.'restful-api',        // Object Relationship Mapping
	));

if( Kohana::$environment == Kohana::PRODUCTION)
{
	Database::$default = 'production';
	Kohana_Exception::$error_view = 'errors/general';
}
else
{
	Database::$default = 'default';
}

require APPPATH . 'vendor/autoload.php';

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */
if (!Route::cache())
{
	Route::set('system', 'system(/<ssname>)')
		->defaults(array(
		  'controller' => 'siggy',
		  'action'     => 'index',
		));

	Route::set('siggyUpdate', 'update')
		->defaults(array(
			'controller' => 'siggy',
			'action' => 'update',
		));

	Route::set('login', 'manage/login')
		->defaults(array(
			'controller' => 'user',
			'action' => 'login',
		));

	Route::set('manage', 'manage(/<controller>(/<action>(/<id>)))')
		->defaults(array(
			'directory'  => 'manage',
			'controller' => 'dashboard',
			'action' => 'index',
		));

	Route::set('api', 'api/<version>/<controller>(/<id>)(.<format>)',
					array(
						'version' => 'v1',
						'format'  => '(json|xml|csv|html)',
					))
		->defaults(array(
			'directory'  => 'api',
			'format' => 'json',
		));

    Route::set('account', 'account(/<action>)')
		->defaults(array(
			'controller' => 'account',
			'action' => 'login'
		));

    Route::set('account', 'account/sso/complete')
		->defaults(array(
			'controller' => 'account',
			'action' => 'sso_complete'
		));
		

	Route::set('stats', 'stats(/year/<year>(/month(/<month>))(/week/<week>))')
		->defaults(array(
			'controller' => 'stats',
			'action' => 'overview',
		));

	Route::set('stats_specific', 'stats/<action>(/year/<year>(/month(/<month>))(/week/<week>))')
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

	if( Kohana::$environment !== Kohana::DEVELOPMENT )
	{
		Route::cache(TRUE);
	}
}
