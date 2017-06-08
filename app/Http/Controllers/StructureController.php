<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Siggy\StandardResponse;
use Siggy\StructureType;
use Siggy\Structure;
use Siggy\StructureVulnerability;
use \Auth;

class StructureController extends Controller {

	public function add(Request $request)
	{
		$postData = json_decode($request->getContent(), true);

		$data = [
			'type_id' => $postData['type_id'],
			'notes' => $postData['notes'],
			'group_id' => Auth::$session->group->id,
			'system_id' => $postData['system_id'],
			'creator_character_id' => Auth::$session->character_id,
			'corporation_name' => $postData['corporation_name'],
		];

		$structure = Structure::create($data);

		return response()->json(StandardResponse::ok($structure));
	}

	public function edit(Request $request)
	{
		$postData = json_decode($request->getContent(), true);
		
		$structure = Structure::findWithSystemByGroup(Auth::$session->group->id, $postData['id']);

		if( $structure == null )
		{
			return response()->json(StandardResponse::error('Structure not found'));
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

		return response()->json(StandardResponse::ok($structure));
	}

	public function remove(Request $request)
	{
		$postData = json_decode($request->getContent(), true);
		
		$structure = Structure::findWithSystemByGroup(Auth::$session->group->id, $postData['id']);

		if( $structure == null )
		{
			return response()->json(StandardResponse::error('Structure not found'));
		}

		$structure->delete();

		$log_message = sprintf("%s deleted structure from system %s", Auth::$session->character_name, $structure->system->name);
		Auth::$session->group->logAction('delpos', $log_message);
		
		return response()->json(StandardResponse::ok());
	}
	
	public function vulnerabilities(Request $request)
	{
		$postData = json_decode($request->getContent(), true);
		
		$structure = Structure::findWithSystemByGroup(Auth::$session->group->id, $postData['id']);

		if( $structure == null )
		{
			return response()->json(StandardResponse::error('Structure not found'));
		}

		StructureVulnerability::where('id', $structure->id)->delete();

		foreach($postData['vulnerabilities'] as $vuln)
		{
			StructureVulnerability::create([
											'id' => $structure->id,
											'day' => $vuln['day'],
											'hour' => $vuln['hour']
											]);
		}


		return response()->json(StandardResponse::ok());
	}
}
