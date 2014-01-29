<?php 

require_once APPPATH.'classes/FrontController.php';
require_once APPPATH.'classes/access.php';

class Controller_Pos extends FrontController
{
	public function action_add()
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
		
		$data = array(
			'pos_location_planet' => $_POST['pos_location_planet'],
			'pos_location_moon' => $_POST['pos_location_moon'],
			'pos_owner' => $_POST['pos_owner'],
			'pos_type' => isset($_POST['pos_type']) ? intval($_POST['pos_type']) : 1,
			'pos_online' => intval($_POST['pos_online']),
			'pos_size' => $_POST['pos_size'],
			'pos_notes' => $_POST['pos_notes'],
			'group_id' => $this->groupData['groupID'],
			'pos_added_date' => time(),
			'pos_system_id' => intval($_POST['pos_system_id'])
		);
		
		if( empty($data['pos_location_planet'] ) || empty($data['pos_location_moon'] ) )
		{
			echo json_encode(array('error' => 1, 'errorMsg' => 'Missing POS Location'));
			exit();
		}
		
		if( !in_array( $data['pos_size'], array('small','medium','large') ) )
		{
			$data['pos_size'] = 'small';
		}
			
		
		$posID = DB::insert('pos_tracker', array_keys($data) )->values(array_values($data))->execute();
		
	}

	public function action_edit()
	{
		
	}
	
	public function action_remove()
	{
		
	}
}