<?php 

require_once APPPATH.'classes/FrontController.php';
require_once APPPATH.'classes/access.php';
require_once APPPATH.'classes/astar.php';
require_once APPPATH.'classes/systempathfinder.php';

class Controller_Chainmap extends FrontController
{
	/*
		Key value array
	*/
	public $template = 'template/public';
	
	protected $output_array = array();
	
	public function action_findNearestExits()
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
		
		
		$target = isset($_REQUEST['target']) ? trim($_REQUEST['target']) : "";
		$targetCurrentSys = isset($_REQUEST['current_system']) ? intval($_REQUEST['current_system']) : 0;
		
		$targetID = 0;
		
		
		if( $targetCurrentSys )
		{
			$targetID = $_SERVER['HTTP_EVE_SOLARSYSTEMID'];
		}
		else if (!empty($target))
		{
			$targetID = mapUtils::findSystemByName($target, $this->groupData['groupID'], $this->groupData['subGroupID'] );
		}
		
		if( $targetID == 0 || $targetID >= 31000000 )
		{
			echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid system'));
			exit();
		}
		
 
		
		$systems = DB::query(Database::SELECT, "( SELECT DISTINCT w.`to` as sys_id,ss.name
												FROM wormholes w 
												LEFT JOIN solarsystems ss ON (ss.id = w.`to`)
												WHERE w.`to`< 31000000 AND w.groupID=:group AND w.subGroupID=:subGroupID)
												UNION DISTINCT
											( SELECT DISTINCT w.`from` as sys_id, ss.name
											FROM wormholes w
											LEFT JOIN solarsystems ss ON (ss.id = w.`from`)
											WHERE w.`from` < 31000000 AND w.groupID=:group AND w.subGroupID=:subGroupID)")
						->param(':group', $this->groupData['groupID'])
						->param(':subGroupID', $this->groupData['subGroupID'])
						->execute()->as_array();
		
		$pather = new SystemPathFinder();
		$result = array();
		foreach($systems as $system)
		{
			$path = $pather->PathFind($targetID, $system['sys_id']);
			
			$result[] = array('system_id' => $system['sys_id'], 'system_name' => $system['name'], 'number_jumps' => count($path) );
		}
		
		usort($result, array('Controller_Chainmap','sortResults'));
		echo json_encode(array('result' => $result));
		exit();
	}
	
	private static  function sortResults($a, $b)
	{
		if ($a['number_jumps'] == $b['number_jumps'])
		{
			return 0;
		}
		return ($a['number_jumps'] < $b['number_jumps']) ? -1 : 1;
	}
}