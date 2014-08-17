<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Api extends Controller
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
	
	public function action_systemsList()
	{
		$results = array();
		
		if( $this->apiKey['subGroupID'] != -1 )
		{
			$mapData = $this->__getChainMapSystemData( $this->apiKey['groupID'], $this->apiKey['subGroupID'] );
			
			$results['groupID'] = $this->apiKey['groupID'];
			
			$results['subGroups'][ intval($this->apiKey['subGroupID']) ]['subGroupID'] = $this->apiKey['subGroupID'];
			foreach( $mapData['systems'] as $system )
			{
				$sys['systemID'] = $system['systemID'];
				$sys['name'] = $system['name'];
				$sys['displayName'] = $system['displayName'];
				$sys['activity'] = $system['activity']; 
		
				$results['subGroups'][ intval($this->apiKey['subGroupID']) ]['systems'][] = $sys;
			}	
		}
		
		if( !count($this->apiErrors) )
		{
			$output = array( 'status' => 'success', 'time' => time(), 'results' => $results );
		}
		else
		{
			$output = array('status' => 'failed', 'time' => time(), 'errors' => $this->apiErrors);
		}
		
		echo json_encode($output);
		exit();
	}
	
	private function __getChainMapSystemData( $groupID, $subGroupID )
	{
		$cache = Cache::instance();
		
		$cacheName = 'mapCache-'.$groupID.'-'.$subGroupID;
		
		if( $mapData = $cache->get( $cacheName, FALSE ) )
		{
			return $mapData;
		}
		else
		{
			$this->apiErrors[] = 'Error: Map data cache for group/subgroup combination does not exist';
			return false;
		}
	}

}