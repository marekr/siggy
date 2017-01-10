<?php

use Illuminate\Database\Capsule\Manager as DB;

class Controller_Pos extends FrontController {
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
			'pos_location_planet' => htmlspecialchars($_POST['pos_location_planet']),
			'pos_location_moon' => htmlspecialchars($_POST['pos_location_moon']),
			'pos_owner' => $_POST['pos_owner'],
			'pos_type' => isset($_POST['pos_type']) ? intval($_POST['pos_type']) : 1,
			'pos_online' => intval($_POST['pos_online']),
			'pos_size' => $_POST['pos_size'],
			'pos_notes' => htmlspecialchars($_POST['pos_notes']),
			'group_id' => Auth::$session->group->id,
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


		$posID = DB::table('pos_tracker')->insert($data);

		Auth::$session->group->incrementStat('pos_adds', Auth::$session->accessData);
	}

	public function action_edit()
	{
		$id = $_POST['pos_id'];

		$pos = DB::selectOne("SELECT pos.pos_id,pos.pos_system_id,ss.name as system_name
										FROM pos_tracker pos
										INNER JOIN solarsystems ss ON ss.id = pos.pos_system_id
										WHERE pos.pos_id=:pos_id AND pos.group_id=:group_id",[
											'group_id' => Auth::$session->group->id,
											'pos_id' => $id
										]);
		if( $pos == null )
		{
			echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid POS ID'));
			exit();
		}

		$data = array(
			'pos_location_planet' => htmlspecialchars($_POST['pos_location_planet']),
			'pos_location_moon' => htmlspecialchars($_POST['pos_location_moon']),
			'pos_owner' => $_POST['pos_owner'],
			'pos_type' => isset($_POST['pos_type']) ? intval($_POST['pos_type']) : 1,
			'pos_online' => intval($_POST['pos_online']),
			'pos_size' => $_POST['pos_size'],
			'pos_notes' => htmlspecialchars($_POST['pos_notes'])
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


		DB::table('pos_tracker')->where('pos_id', '=', $pos->pos_id)->update( $data );

		Auth::$session->group->incrementStat('pos_updates', Auth::$session->accessData);

		$log_message = sprintf("%s edit POS in system %s", Auth::$session->character_name, $pos->system_name);
		Auth::$session->group->logAction('editpos', $log_message);
	}

	public function action_remove()
	{
		$id = $_POST['pos_id'];

		$pos = DB::selectOne("SELECT pos.pos_id,pos.pos_system_id,ss.name as system_name
										FROM pos_tracker pos
										INNER JOIN solarsystems ss ON ss.id = pos.pos_system_id
										WHERE pos.pos_id=:pos_id AND pos.group_id=:group_id",[
											'group_id' => Auth::$session->group->id,
											'pos_id' => $id
										]);
		if( $pos == null )
		{
			echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid POS ID'));
			exit();
		}

		DB::table('pos_tracker')->where('pos_id', '=', $id)->delete();

		$log_message = sprintf("%s deleted POS from system %s", Auth::$session->character_name, $pos->system_name);
		Auth::$session->group->logAction('delpos', $log_message);
	}
}
