<?php

use Illuminate\Database\Capsule\Manager as DB;

class Controller_Pos extends FrontController {
	public function action_add()
	{
		$this->profiler = NULL;
		$this->response->noCache();


		if(	 !$this->siggyAccessGranted() )
		{
			echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid auth'));
			exit();
		}

		$data = [
			'location_planet' => htmlspecialchars($_POST['pos_location_planet']),
			'location_moon' => htmlspecialchars($_POST['pos_location_moon']),
			'owner' => $_POST['pos_owner'],
			'pos_type_id' => isset($_POST['pos_type']) ? intval($_POST['pos_type']) : 1,
			'online' => intval($_POST['pos_online']),
			'size' => $_POST['pos_size'],
			'notes' => htmlspecialchars($_POST['pos_notes']),
			'group_id' => Auth::$session->group->id,
			'added_date' => time(),
			'system_id' => intval($_POST['pos_system_id'])
		];

		if( empty($data['location_planet'] ) || empty($data['location_moon'] ) )
		{
			$this->response->json(['error' => 1, 'errorMsg' => 'Missing POS Location']);
			return;
		}

		if( !in_array( $data['size'], ['small','medium','large'] ) )
		{
			$data['size'] = 'small';
		}

		POS::create($data);

		Auth::$session->group->incrementStat('pos_adds', Auth::$session->accessData);

		$this->response->json(true);
	}

	public function action_edit()
	{
		$this->profiler = NULL;
		$this->response->noCache();

		$id = $_POST['pos_id'];

		$pos = POS::findWithSystemByGroup(Auth::$session->group->id, $id);

		if( $pos == null )
		{
			$this->response->json(['error' => 1, 'errorMsg' => 'Invalid POS ID']);
			return;
		}

		$data = array(
			'location_planet' => htmlspecialchars($_POST['pos_location_planet']),
			'location_moon' => htmlspecialchars($_POST['pos_location_moon']),
			'owner' => $_POST['pos_owner'],
			'pos_type_id' => isset($_POST['pos_type']) ? intval($_POST['pos_type']) : 1,
			'online' => intval($_POST['pos_online']),
			'size' => $_POST['pos_size'],
			'notes' => htmlspecialchars($_POST['pos_notes'])
		);

		if( empty($data['location_planet'] ) || empty($data['location_moon'] ) )
		{
			$this->response->json(['error' => 1, 'errorMsg' => 'Missing POS Location']);
			return;
		}

		if( !in_array( $data['size'], ['small','medium','large'] ) )
		{
			$data['size'] = 'small';
		}

		$pos->fill($data);
		$pos->save();

		Auth::$session->group->incrementStat('pos_updates', Auth::$session->accessData);

		$log_message = sprintf("%s edited POS in system %s", Auth::$session->character_name, $pos->system->name);
		Auth::$session->group->logAction('editpos', $log_message);

		$this->response->json(true);
	}

	public function action_remove()
	{
		$this->profiler = NULL;
		$this->response->noCache();

		$id = $_POST['pos_id'];

		$pos = POS::findWithSystemByGroup(Auth::$session->group->id, $id);

		if( $pos == null )
		{
			$this->response->json(['error' => 1, 'errorMsg' => 'Invalid POS ID']);
			return;
		}

		$pos->delete();

		$log_message = sprintf("%s deleted POS from system %s", Auth::$session->character_name, $pos->system->name);
		Auth::$session->group->logAction('delpos', $log_message);
		
		$this->response->json(true);
	}
}
