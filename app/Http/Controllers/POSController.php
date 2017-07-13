<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Siggy\StandardResponse;
use Siggy\POS;
use App\Facades\Auth;

class POSController extends Controller {

	public function add(Request $request)
	{
		$postData = json_decode($request->getContent(), true);

		$data = [
			'location_planet' => htmlspecialchars($postData['location_planet']),
			'location_moon' => htmlspecialchars($postData['location_moon']),
			'owner' => $postData['owner'],
			'type_id' => isset($postData['type_id']) ? intval($postData['type_id']) : 1,
			'online' => intval($postData['online']),
			'size' => $postData['size'],
			'notes' => htmlspecialchars($postData['notes']),
			'group_id' => Auth::session()->group->id,
			'added_date' => time(),
			'system_id' => intval($postData['system_id'])
		];

		if( empty($data['location_planet'] ) || empty($data['location_moon'] ) )
		{
			return response()->json(['error' => 1, 'errorMsg' => 'Missing POS Location']);
		}

		if( !in_array( $data['size'], ['small','medium','large'] ) )
		{
			$data['size'] = 'small';
		}

		POS::create($data);

		Auth::session()->group->incrementStat('pos_adds', Auth::session()->accessData);

		return response()->json(true);
	}

	public function edit(Request $request)
	{
		$postData = json_decode($request->getContent(), true);

		$pos = POS::findWithSystemByGroup(Auth::session()->group->id, $postData['id']);

		if( $pos == null )
		{
			return response()->json(StandardResponse::error('POS not found'));
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

		Auth::session()->group->incrementStat('pos_updates', Auth::session()->accessData);

		$log_message = sprintf("%s edited POS in system %s", Auth::session()->character_name, $pos->system->name);
		Auth::session()->group->logAction('editpos', $log_message);

		return response()->json(StandardResponse::ok($pos));
	}

	public function remove(Request $request)
	{
		$postData = json_decode($request->getContent(), true);

		$pos = POS::findWithSystemByGroup(Auth::session()->group->id, $postData['id']);

		if( $pos == null )
		{
			return response()->json(StandardResponse::error('POS not found'));
		}

		$pos->delete();

		$log_message = sprintf("%s deleted POS from system %s", Auth::session()->character_name, $pos->system->name);
		Auth::session()->group->logAction('delpos', $log_message);
		
		return response()->json(StandardResponse::ok());
	}
}
