<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Siggy\StandardResponse;
use Siggy\StructureType;
use Siggy\Structure;
use Siggy\StructureVulnerability;
use App\Facades\Auth;
use App\Facades\SiggySession;

class StructureController extends Controller {

	public function add(Request $request)
	{
		$postData = json_decode($request->getContent(), true);

		$data = [
			'type_id' => $postData['type_id'],
			'notes' => $postData['notes'],
			'group_id' => SiggySession::getGroup()->id,
			'system_id' => $postData['system_id'],
			'creator_character_id' => SiggySession::getCharacterId(),
			'corporation_name' => $postData['corporation_name'],
		];

		$structure = Structure::create($data);

		return response()->json(StandardResponse::ok($structure));
	}

	public function edit(Request $request)
	{
		$postData = json_decode($request->getContent(), true);
		
		$structure = Structure::findWithSystemByGroup(SiggySession::getGroup()->id, $postData['id']);

		if( $structure == null )
		{
			return response()->json(StandardResponse::error('Structure not found'));
		}
		
		$data = [
			'type_id' => $postData['type_id'],
			'notes' => $postData['notes'],
			'corporation_name' => $postData['corporation_name'],
			'creator_id' => SiggySession::getCharacterId()
		];

		$structure->fill($data);
		$structure->save();

		$log_message = sprintf("%s edited structure in system %s", SiggySession::getCharacterName(),  $structure->system->name);
		SiggySession::getGroup()->logAction('editpos', $log_message);

		return response()->json(StandardResponse::ok($structure));
	}

	public function remove(Request $request)
	{
		$postData = json_decode($request->getContent(), true);
		
		$structure = Structure::findWithSystemByGroup(SiggySession::getGroup()->id, $postData['id']);

		if( $structure == null )
		{
			return response()->json(StandardResponse::error('Structure not found'));
		}

		$structure->delete();

		$log_message = sprintf("%s deleted structure from system %s", SiggySession::getCharacterName(), $structure->system->name);
		SiggySession::getGroup()->logAction('delpos', $log_message);
		
		return response()->json(StandardResponse::ok());
	}
	
	public function vulnerabilities(Request $request)
	{
		$postData = json_decode($request->getContent(), true);
		
		$structure = Structure::findWithSystemByGroup(SiggySession::getGroup()->id, $postData['id']);

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
