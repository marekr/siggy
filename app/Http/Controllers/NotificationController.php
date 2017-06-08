<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use \Auth;
use \CharacterGroup;
use \Notifier;
use \NotificationTypes;
use Siggy\Notification;
use \System;

class NotificationController extends Controller {

	public function read()
	{
		$characterGroup = CharacterGroup::find(Auth::$session->character_id, Auth::$session->group->id);
		if($characterGroup == null)
		{
			$characterGroup = CharacterGroup::create(['character_id' => Auth::$session->character_id, 'group_id' => Auth::$session->group->id]);
		}

		$characterGroup->last_notification_read = time();
		$characterGroup->save();
	}

	public function notifiers()
	{
		$data = Notifier::allByGroupCharacter(Auth::$session->group->id, Auth::$session->character_id);

		return response()->json($data);
	}

	public function all()
	{
		$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
		if( $page < 1)
		{
			$page = 1;
		}

		$numberPerPage = 50;
		$offset = $numberPerPage*($page-1);
		$data = Notification::latest(0, Auth::$session->group->id, Auth::$session->character_id, $offset, $numberPerPage);

		$totalPages = ceil(Notification::total(0, Auth::$session->group->id, Auth::$session->character_id) / $numberPerPage);
		$response = array(
			'items' => $data,
			'total_pages' => $totalPages
		);
		
		return response()->json($response);
	}

	public function notifiers_add(Request $request)
	{
		$scope = $request->input('scope', null);

		if( $scope == null || ($scope != 'personal' && $scope != 'group') )
		{
			//error
			return response()->json(['error' => 1, 'error_message' => 'Invalid scope']);
		}

		$type = $request->input('type', null);

		if( $type == null || !in_array($type, NotificationTypes::asArray()) )
		{
			//error
			return response()->json(['error' => 1, 'error_message' => 'Invalid type']);
		}

		$data = $request->input('notifier');

		if( $type == NotificationTypes::SystemMappedByName )
		{
			$system = System::findByName($data['system_name']);
			if($system != null)
			{
				$data['system_id'] = $system->id;
			}
			else
			{
				//error
				return response()->json(['error' => 1, 'error_message' => 'Invalid system']);
			}
		}
		else if( $type == NotificationTypes::SystemMapppedWithResident )
		{
			$data['include_offline'] = (isset($data['include_offline']) && $data['include_offline']) ? true : false;
		}

		$notifier = Notifier::createFancy($type, $scope, Auth::$session->group->id, Auth::$session->character_id, $data);
	}

	public function notifiers_delete(Request $request)
	{
		$id = $request->input('id', null);

		if( $id == null )
		{
			return response()->json(['error' => 1, 'error_message' => 'ID missing']);
		}

		Notifier::deleteByIdGroupCharacter( $id, Auth::$session->group->id, Auth::$session->character_id );
	}
}
