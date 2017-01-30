<?php

class Controller_Notifications extends FrontController {

	public function action_read()
	{
		$characterGroup = CharacterGroup::find(Auth::$session->character_id, Auth::$session->group->id);
		if($characterGroup == null)
		{
			$characterGroup = CharacterGroup::create(['character_id' => Auth::$session->character_id, 'group_id' => Auth::$session->group->id]);
		}

		$characterGroup->last_notification_read = time();
		$characterGroup->save();
	}

	public function action_notifiers()
	{
		$data = Notifier::allByGroupCharacter(Auth::$session->group->id, Auth::$session->character_id);

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
		$data = Notification::latest(0, Auth::$session->group->id, Auth::$session->character_id, $offset, $numberPerPage);

		$totalPages = ceil(Notification::total(0, Auth::$session->group->id, Auth::$session->character_id) / $numberPerPage);
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
			$system = System::findByName($data['system_name']);
			if($system != null)
			{
				$data['system_id'] = $system->id;
			}
			else
			{
				//error
				echo json_encode(array('error' => 1, 'error_message' => 'Invalid system'));
				exit();
			}
		}
		else if( $type == NotificationTypes::SystemMapppedWithResident )
		{
			$data['include_offline'] = (isset($data['include_offline']) && $data['include_offline']) ? true : false;
		}

		$notifier = Notifier::createFancy($type, $scope, Auth::$session->group->id, Auth::$session->character_id, $data);
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

		Notifier::deleteByIdGroupCharacter( $id, Auth::$session->group->id, Auth::$session->character_id );
	}
}
