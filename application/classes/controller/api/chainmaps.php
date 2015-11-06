<?php

class Controller_Api_Chainmaps extends Controller_Api
{
	// GET
	public function action_index()
	{
		try
		{
			$data = groupUtils::getGroupData($this->_user->_id);
			$chainMapID = $this->request->param('id', 0);

			$output = [];
			if( $chainMapID == 0 )
			{
				foreach($data['chainmaps'] as $c)
				{
					$hs = explode(",", $c['chainmap_homesystems']);
					$output[] = [
									'id' => (int)$c['chainmap_id'],
									'name' => $c['chainmap_name'],
									'hs' => $hs
								];
				}
			}
			else
			{
				$chainmap = null;
				
				try
				{
					$chainmap = new Chainmap($chainMapID, $this->_user->_id);
				}
				catch(Exception $e)
				{
					$this->_error($e);
					return;
				}
				$data = $chainmap->get_map_cache();
				
				foreach($data['wormholes'] as $w)
				{
					$output['wormholes'][] = [
												'hash' => $w['hash'],
												'to_system_id' => (int)$w['to_system_id'],
												'from_system_id' => (int)$w['from_system_id'],
												'eol' => (int)$w['eol'],
												'mass' => (int)$w['mass'],
												'frigate_sized' => (bool)$w['frigate_sized'],
												'created_at' => $w['created_at'],
												'updated_at' => $w['updated_at'],
												'total_tracked_mass' => (int)$w['total_tracked_mass'],
											];
				}
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
}
