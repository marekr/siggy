<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Api extends Controller_Rest	
{
	/**
	 * A Restexample model instance for all the business logic.
	 *
	 * @var Model_Restexample
	 */
	protected $_rest;
	protected $_auth_type = RestUser::AUTH_TYPE_HASH;
	protected $_auth_source = RestUser::AUTH_SOURCE_GET | RestUser::AUTH_SOURCE_HEADER;
	
	private $apiErrors = array();
	private $apiKey = array();
	
	
	function before()
	{
		parent::before();
	}
}