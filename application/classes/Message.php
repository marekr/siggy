<?php

class Message {
	
	public static function add(string $type, string $message)
	{
		$session = Session::instance();

		$messages = json_decode($session->get('messages',''), true);

		if(!is_array($messages))
		{
			$messages = [];
		}

		// append to messages
		$messages[$type][] = $message;

		// set messages
		$session->set('messages', json_encode($messages));
 	}

	public static function count()
	{
		$session = Session::instance();

		$messages = json_decode($session->get('messages',''), true);
		if(!is_array($messages))
		{
			$messages = [];
		}

		return count($messages);
	}

	public static function output()
	{
		$session = Session::instance();

		$str = '';

		$data = $session->get_once('messages','');
		$allMessages = json_decode($data, true);

		if(!is_array($allMessages))
		{
			$allMessages = [];
		}

		if(!empty($allMessages))
		{
			foreach($allMessages as $type => $messages)
			{
				if( $type == 'error' )
				{
					$type = 'danger';
				}

				foreach($messages as $message)
				{
					$str .= '<div class="alert alert-'.$type.'">'.$message.'</div>';
				}
			}
		}

		return $str;
	}

}
