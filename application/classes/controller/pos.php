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
		
		miscUtils::increment_stat('pos_adds', $this->groupData);
	}

	public function action_edit()
	{
		$id = $_POST['pos_id'];
		
		$pos = DB::query(Database::SELECT, "SELECT pos.pos_id,pos.pos_system_id,ss.name as system_name
										FROM pos_tracker pos
										INNER JOIN solarsystems ss ON ss.id = pos.pos_system_id
										WHERE pos.pos_id=:pos_id AND pos.group_id=:group_id")
								->param(':group_id', $this->groupData['groupID'])
								->param(':pos_id', $id)
								->execute()->current();
								
		if( !isset($pos['pos_id']) )
		{
			echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid POS ID'));
			exit();
		}
		
		$data = array(
			'pos_location_planet' => $_POST['pos_location_planet'],
			'pos_location_moon' => $_POST['pos_location_moon'],
			'pos_owner' => $_POST['pos_owner'],
			'pos_type' => isset($_POST['pos_type']) ? intval($_POST['pos_type']) : 1,
			'pos_online' => intval($_POST['pos_online']),
			'pos_size' => $_POST['pos_size'],
			'pos_notes' => $_POST['pos_notes']
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
			
			
		DB::update('pos_tracker')->set( $data )->where('pos_id', '=', $pos['pos_id'])->execute();
		
		miscUtils::increment_stat('pos_updates', $this->groupData);
		
		$log_message = sprintf("%s edit POS in system %s", $this->groupData['charName'], $pos['system_name']);
		groupUtils::log_action($this->groupData['groupID'], 'delpos', $log_message);
	}
	
	public function action_remove()
	{
		$id = $_POST['pos_id'];
		
		$pos = DB::query(Database::SELECT, "SELECT pos.pos_id,pos.pos_system_id,ss.name as system_name
										FROM pos_tracker pos
										INNER JOIN solarsystems ss ON ss.id = pos.pos_system_id
										WHERE pos.pos_id=:pos_id AND pos.group_id=:group_id")
								->param(':group_id', $this->groupData['groupID'])
								->param(':pos_id', $id)
								->execute()->current();
								
		if( !isset($pos['pos_id']) )
		{
			echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid POS ID'));
			exit();
		}
		
		DB::delete('pos_tracker')->where('pos_id', '=', $id)->execute();
		
		$log_message = sprintf("%s deleted POS from system %s", $this->groupData['charName'], $pos['system_name']);
		groupUtils::log_action($this->groupData['groupID'], 'delpos', $log_message);
	}
}