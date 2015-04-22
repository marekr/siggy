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

	public function action_create_alert()
	{

	}

	public function action_delete_alert()
	{

	}
}
