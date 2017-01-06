<?php

class Controller_Notifications extends FrontController {

	public function action_read()
	{
		$characterGroup = CharacterGroup::find(Auth::$session->charID, Auth::$session->group->id);
		if($characterGroup == null)
		{
			$characterGroup = new CharacterGroup();
			$characterGroup->character_id = Auth::$session->charID;
			$characterGroup->group_id = Auth::$session->group->id;
		}

		$characterGroup->save(['last_notification_read' => time()]);
	}

	public function action_notifiers()
	{
		$data = Notifier::all(Auth::$session->group->id, Auth::$session->charID);

		echo json_encode($data);
		exit();
	}

	public function action_all()
	{
		$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
		if( $page < 1)
		{
			$page = 1;
		}

		$numberPerPage = 50;
		$offset = $numberPerPage*($page-1);
		$data = Notification::latest(0, Auth::$session->group->id, Auth::$session->charID, $offset, $numberPerPage);

		$totalPages = ceil(Notification::total(0, Auth::$session->group->id, Auth::$session->charID) / $numberPerPage);
		$response = array(
			'items' => $data,
			'total_pages' => $totalPages
		);
		echo json_encode($response);
		exit();
	}

	public function action_notifiers_add()
	{
		$scope = $this->request->post('scope');

		if( $scope == NULL || ($scope != 'personal' && $scope != 'group') )
		{
			//error
			echo json_encode(array('error' => 1, 'error_message' => 'Invalid scope'));
			exit();
		}

		$type = $this->request->post('type');

		if( $type == NULL || !in_array($type, NotificationTypes::asArray()) )
		{
			//error
			echo json_encode(array('error' => 1, 'error_message' => 'Invalid type'));
			exit();
		}

		$data = $this->request->post('notifier');

		if( $type == NotificationTypes::SystemMappedByName )
		{
			$data['system_id'] = miscUtils::findSystemByName($data['system_name']);
		}
		else if( $type == NotificationTypes::SystemMapppedWithResident )
		{
			$data['include_offline'] = (isset($data['include_offline']) && $data['include_offline']) ? true : false;
		}

		$notifier = Notifier::create($type, $scope, Auth::$session->group->id, Auth::$session->charID, $data);
	}

	public function action_notifiers_edit()
	{

	}

	public function action_notifiers_delete()
	{
		$id = $this->request->post('id');

		if( $id == NULL )
		{
			//error
			echo json_encode(array('error' => 1, 'error_message' => 'ID missing'));
			exit();
		}

		Notifier::delete( $id, Auth::$session->group->id, Auth::$session->charID );
	}
}
