<?php

use Illuminate\Database\Capsule\Manager as DB;

use Siggy\StandardResponse;
use Siggy\POS;

class Controller_Pos extends FrontController {
	public function action_add()
	{
		$this->profiler = NULL;
		$this->response->noCache();

		if( !$this->siggyAccessGranted() )
		{
			$this->response->json(StandardResponse::error('Invalid Auth'));
			return;
		}

		$postData = json_decode($this->request->body(), true);

		$data = [
			'location_planet' => htmlspecialchars($postData['location_planet']),
			'location_moon' => htmlspecialchars($postData['location_moon']),
			'owner' => $postData['owner'],
			'type_id' => isset($postData['type_id']) ? intval($postData['type_id']) : 1,
			'online' => intval($postData['online']),
			'size' => $postData['size'],
			'notes' => htmlspecialchars($postData['notes']),
			'group_id' => Auth::$session->group->id,
			'added_date' => time(),
			'system_id' => intval($postData['system_id'])
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

		if( !$this->siggyAccessGranted() )
		{
			$this->response->json(StandardResponse::error('Invalid Auth'));
			return;
		}

		$postData = json_decode($this->request->body(), true);

		$pos = POS::findWithSystemByGroup(Auth::$session->group->id, $postData['id']);

		if( $pos == null )
		{
			$this->response->json(StandardResponse::error('POS not found'));
			return;
		}

		$data = [
			'location_planet' => htmlspecialchars($postData['location_planet']),
			'location_moon' => htmlspecialchars($postData['location_moon']),
			'owner' => $postData['owner'],
			'type_id' => isset($postData['type_id']) ? intval($postData['type_id']) : 1,
			'online' => intval($postData['online']),
			'size' => $postData['size'],
			'notes' => htmlspecialchars($postData['notes'])
		];

		if( !in_array( $data['size'], ['small','medium','large'] ) )
		{
			$data['size'] = 'small';
		}

		$pos->fill($data);
		$pos->save();

		Auth::$session->group->incrementStat('pos_updates', Auth::$session->accessData);

		$log_message = sprintf("%s edited POS in system %s", Auth::$session->character_name, $pos->system->name);
		Auth::$session->group->logAction('editpos', $log_message);

		$this->response->json(StandardResponse::ok($pos));
	}

	public function action_remove()
	{
		$this->profiler = NULL;
		$this->response->noCache();

		if( !$this->siggyAccessGranted() )
		{
			$this->response->json(StandardResponse::error('Invalid Auth'));
			return;
		}
		
		$postData = json_decode($this->request->body(), true);

		$pos = POS::findWithSystemByGroup(Auth::$session->group->id, $postData['id']);

		if( $pos == null )
		{
			$this->response->json(StandardResponse::error('POS not found'));
			return;
		}

		$pos->delete();

		$log_message = sprintf("%s deleted POS from system %s", Auth::$session->character_name, $pos->system->name);
		Auth::$session->group->logAction('delpos', $log_message);
		
		$this->response->json(StandardResponse::ok());
	}
}
