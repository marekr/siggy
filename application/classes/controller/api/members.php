<?php

class Controller_Api_Members extends Controller_Api
{
	// GET
	public function action_index()
	{
		try
		{
			$group = Group::find($this->_user->_id);

			$output = array();
			$output['total'] = count($group->groupMembers());
			foreach( $group->groupMembers() as $member )
			{
				$memberEntry = array(
						'id' => (int)$member->id,
						'eve_id' => (int)$member->eveID,
						'type' => $member->memberType
				);

				$chainmaps = array();
				foreach( $group->chainMaps() as $chainmap )
				{
					foreach( $chainmap->access as $access )
					{
						if( $access->eveID == $memberEntry['eve_id']
						&& $access->memberType == $memberEntry['type'])
						{
							$chainmaps[] = (int)$chainmap->chainmap_id;
						}
					}
				}
				$memberEntry['chainmaps'] = array_unique($chainmaps);
				$output['members'][] = $memberEntry;
			}

			$this->rest_output( $output );
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
