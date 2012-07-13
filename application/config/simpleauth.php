<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
/**
 * Type of hash to use for passwords. Any algorithm supported by the hash function
 * can be used here. Note that the length of your password is determined by the
 * hash type + the number of salt characters.
 * @see http://php.net/hash
 * @see http://php.net/hash_algos
 */
'hash_method' => 'sha256',

/**
 * Defines the secret string added to password (as prefix) before hashing
 */
//'salt_prefix' => 'simple_auth_secret',

/**
 * Defines the secret string added to password (as suffix) before hashing
 */
//'salt_suffix' => '_secret',
//
'hash_key' => '876D309BE9025C2F2A2C0532F9BAA0784F23139C31FF9BC515ED3FCFA10580DC',
/**
 * Set the auto-login (remember me) cookie lifetime, in seconds. The default
 * lifetime is two weeks.
 */
'lifetime' => 1209600,

/**
 * Set the session key that will be used to store the current user.
 */
'session_key' => 'auth_user',

/**
 * Set the cookie that will be used to store the current user.
 */
'cookie_key' => 'auth_auto_login',

/**
 * default roles, values must be empty.
 */
'roles' => array('admin'=>'','active'=>'','moderator'=>''),

/**
 * password field name
 */
'password' => 'password',

/**
 * unique field checked as username
 */
'unique' => 'username',

/**
 * unique field checked when creating user, to prevent duplicating email or username value
 */
'unique_second' => 'email',

/**
 * primary key for auth_users table
 */
'primary_key' => 'id'
);