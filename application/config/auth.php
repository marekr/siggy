<?php defined('SYSPATH') or die('No direct script access.');
return array
(
	'driver' => 'ORM',
	'hash_method' => 'sha256',
	'hash_key' => '876D309BE9025C2F2A2C0532F9BAA0784F23139C31FF9BC515ED3FCFA10580DC', // replace with random string
	'lifetime' => 1209600,
	'session_key' => 'auth_user',
	'users' => array(),
);