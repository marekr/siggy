<?php

class Controller_Notifications extends FrontController {

	public function action_read()
	{
		DB::query(Database::INSERT, 'INSERT INTO character_group (`id`, `group_id`,`last_notification_read`)
									VALUES(:char, :group, :last)
									ON DUPLICATE KEY UPDATE last_notification_read=:last')
					->param(':char', Auth::$session->charID )
					->param(':group', Auth::$session->groupID )
					->param(':last', time() )
					->execute();
	}

	public function action_notifiers()
	{
		$data = Notifier::all(Auth::$session->groupID, Auth::$session->charID);

		echo json_encode($data);
		exit();
	}

	public function action_all()
	{
		$data = Notification::latest(0, Auth::$session->groupID, Auth::$session->charID, 50);

		echo json_encode($data);
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

		$notifier = Notifier::create($type, $scope, Auth::$session->groupID, Auth::$session->charID, $data);
	}

	public function action_notifiers_edit()
	{

	}
}
