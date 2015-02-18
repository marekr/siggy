<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Api extends Controller_REST	
{

	private $apiErrors = array();
	private $apiKey = array();
	
	function __construct(Kohana_Request $request, Kohana_Response $response)
	{
		parent::__construct($request, $response);
	}
	
	function before()
	{
		$API_access_result = $this->setup_API();
		
		if( !$API_access_result  )
		{
			$output = array('status' => 'failed', 'time' => time(), 'errors' => $this->apiErrors);
			echo json_encode($output);
			exit();
		}
	}
	
	public function setup_API()
	{
		if( empty( $_REQUEST['keyID'] ) )
		{
			$this->apiErrors[] = 'keyID must not be empty!';
			return false;
		}
		
		if( empty($_REQUEST['keyCode'] ) )
		{
			$this->apiErrors[] = 'keyCode must not be empty!';
			return false;
		}
		
		$api = DB::query(Database::SELECT, 'SELECT * FROM	siggyapikeys WHERE keyID = :keyID AND keyCode = :keyCode')->param(':keyID', intval($_REQUEST['keyID']) )->param(':keyCode', $_REQUEST['keyCode'])->execute()->current();

		if( isset( $api['keyID'] ) )
		{
			$this->apiKey = $api;
			return true;
		}
		else
		{
			return false;
		}
	}
}