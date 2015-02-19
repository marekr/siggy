<?php

class Controller_Api_Members extends Controller_Api
{
	// GET
	public function action_index()
	{
		try
		{
			$this->rest_output( array(
				'total' => 120,
				'members' => array( 
					array( 'type' => 'char',
							'id' => 4125121,
							'eve_id' => 12512512,
							'chainmaps' => array(0,1,5) ),
					array( 'type' => 'char',
							'id' => 4125121,
							'eve_id' => 12512512 ),
					array( 'type' => 'char',
							'id' => 4125121,
							'eve_id' => 12512512 ),
				)
			) );
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