<?php

use Illuminate\Database\Capsule\Manager as DB;

use Siggy\StandardResponse;
use Siggy\StructureType;
use Siggy\Structure;

class Controller_Structure extends FrontController {
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
			'type_id' => $postData['type_id'],
			'notes' => $postData['notes'],
			'group_id' => Auth::$session->group->id,
			'system_id' => $postData['system_id'],
			'creator_character_id' => Auth::$session->character_id,
			'corporation_name' => $postData['corporation_name'],
		];

		$structure = Structure::create($data);

		$this->response->json(StandardResponse::ok($structure));
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
		
		$structure = Structure::findWithSystemByGroup(Auth::$session->group->id, $postData['id']);

		if( $structure == null )
		{
			$this->response->json(StandardResponse::error('Structure not found'));
			return;
		}
		
		$data = [
			'type_id' => $postData['type_id'],
			'notes' => $postData['notes'],
			'corporation_name' => $postData['corporation_name'],
			'creator_id' => Auth::$session->character_id
		];

		$structure->fill($data);
		$structure->save();

		$log_message = sprintf("%s edited structure in system %s", Auth::$session->character_name,  $structure->system->name);
		Auth::$session->group->logAction('editpos', $log_message);

		$this->response->json(StandardResponse::ok($structure));
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
		
		$structure = Structure::findWithSystemByGroup(Auth::$session->group->id, $postData['id']);

		if( $structure == null )
		{
			$this->response->json(StandardResponse::error('Structure not found'));
			return;
		}

		$structure->delete();

		$log_message = sprintf("%s deleted structure from system %s", Auth::$session->character_name, $structure->system->name);
		Auth::$session->group->logAction('delpos', $log_message);
		
		$this->response->json(StandardResponse::ok());
	}
}
