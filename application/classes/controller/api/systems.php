<?php

class Controller_Api_Systems extends Controller_Api
{
	// GET
	public function action_index()
	{


		try
		{
			$systemID = $this->request->param('id', 0);
			$groupID = (int)$this->_user->_id;
			print "<pre>";
			if( $systemID != 0 )
			{
				$system = new System();
				$output = $system->get($systemID, $groupID);

				$this->rest_output( $output );
			}
		}
		catch (Kohana_HTTP_Exception $khe)
		{
			$this->_error($khe);
			return;
		}
		catch (Kohana_Exception $e)
		{
			$this->_error('An internal error has occurred', 500);
			throw $e;
		}
	}

	// POST
	public function action_create()
	{
	}

	// PUT
	public function action_update()
	{
	}

	// DELETE
	public function action_delete()
	{
	}
}
