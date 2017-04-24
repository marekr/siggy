<?php defined('SYSPATH') or die('No direct script access.');
return array
(
	'default'    => array
	(
		'driver'             => 'file',
		'cache_dir'          => DOCROOT.'storage/cache',
		'default_expire'     => 3600,
		'ignore_on_delete'   => array()
	),
	'memcache'	=> array(
		'driver' => 'memcache',
		'servers' => array(
			array(
				 'host'             => 'localhost',
				 'port'             => 11211,
				 'persistent'       => FALSE,
				 'weight'           => 1,
				 'timeout'          => 30,
				 'retry_interval'   => 15,
				 'status'           => TRUE,
				 'instant_death' => FALSE
			)
		),
		'compression' => 'false'
   )
);