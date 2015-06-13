<?php

class Controller_Astrolabe extends FrontController {

	public function before()
    {
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		header('content-type: application/json');
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1\


		if(	 !$this->siggyAccessGranted() )
		{
			echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid auth'));
			exit();
		}

		if( Kohana::$environment == Kohana::PRODUCTION )
		{
			header('content-type: application/json');
			ob_start( 'ob_gzhandler' );
		}
	}

	public function action_route()
	{
		$waypoints = (isset($_REQUEST['waypoints']) ? json_decode($_REQUEST['waypoints']) : null);
		if( $waypoints == null )
			die();

		$paths = [];

		$pather = new Pathfinder();
		for( $i = 0; $i < count($waypoints) - 1; $i++ )
		{
			$sourceID = mapUtils::findSystemByName($waypoints[$i]->system_name, Auth::$session->groupID, Auth::$session->accessData['active_chain_map'] );
			$targetID = mapUtils::findSystemByName($waypoints[$i+1]->system_name, Auth::$session->groupID, Auth::$session->accessData['active_chain_map'] );

			$path = $pather->shortest($sourceID, $targetID);

			$parts = [];
			foreach(explode(',',$path['jumps']) as $systemJumpID)
			{
				$parts[] = System::get($systemJumpID, Auth::$session->groupID, 'basic');
			}
			$paths[] = $parts;
		}

		print json_encode( [ 'paths' => $paths ] );
		die();
	}
}
