<?php

class Message
{
	public static function add($type, $message)
	{
		@session_start();
		// get session messages
		if( isset($_SESSION['messages']) )
		{
			$messages = $_SESSION['messages'];
		}
		else
		{
			$messages = array();
		}

		// append to messages
		$messages[$type][] = $message;

		// set messages
		$_SESSION['messages'] = $messages;
	}

	public static function count()
	{
		@session_start();
		$count = 0;

		if( isset($_SESSION['messages']) )
		{
			$count = count($_SESSION['messages']);
		}

		return $count;
	}

	public static function output()
	{
		$str = '';
		$messages = $_SESSION['messages'];
		unset($_SESSION['messages']);

		if(!empty($messages))
		{
			foreach($messages as $type => $messages)
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