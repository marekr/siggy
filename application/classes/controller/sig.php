<?php

class Controller_Sig extends FrontController {

	public function before()
	{
		parent::before();

		if( Auth::$session->accessData['active_chain_map'] )
		{
			$this->chainmap = new Chainmap(Auth::$session->accessData['active_chain_map'], Auth::$session->groupID);
		}
	}

	public function action_add()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		$this->response->headers('Content-Type','application/json');
		$this->response->headers('Cache-Control','no-cache, must-revalidate');

		if(	 !$this->siggyAccessGranted() )
		{
			$this->response->body(json_encode(['error' => 1, 'errorMsg' => 'Invalid auth']));
			return;
		}

		$sigData = json_decode($this->request->body(), true);

		if( !empty($sigData) && isset($sigData['systemID']) )
		{
			$insert['systemID'] = intval($sigData['systemID']);
			$insert['sig'] = strtoupper($sigData['sig']);
			$insert['description'] = $sigData['desc'];
			$insert['created'] = time();
			$insert['siteID'] = intval($sigData['siteID']);
			$insert['type'] = $sigData['type'];
			$insert['groupID'] = Auth::$session->groupID;

			if( Auth::$session->accessData['showSigSizeCol'] )
			{
				$insert['sigSize'] = ( is_numeric( $sigData['sigSize'] ) ? $sigData['sigSize'] : '' );
			}

			$insert['creator'] = Auth::$session->charName;

			$sigID = DB::insert('systemsigs', array_keys($insert) )->values(array_values($insert))->execute();

			$this->chainmap->update_system($insert['systemID'], array('lastUpdate' => time(),
																'lastActive' => time() )
										);

			miscUtils::increment_stat('adds', Auth::$session->accessData);

			$this->notifierCheck($insert);

			$insert['sigID'] = $sigID[0];
			$this->response->body(json_encode(array($sigID[0] => $insert )));
		}
	}

	private function notifierCheck($sigData)
	{
		foreach( Auth::$session->accessData['notifiers'] as $notifier )
		{
			if( $notifier['type'] == NotificationTypes::SiteFound )
			{
				$data = json_decode($notifier['data']);
				if( $sigData['siteID'] == $data->site_id )
				{
					$eventData = array(
										'system_id' => $sigData['systemID'],
										'system_name' => miscUtils::systemNameByID($sigData['systemID']),
										'site_id' => $sigData['siteID'],
										'discoverer_name' => Auth::$session->charName,
										'discoverer_id' => Auth::$session->charID,
										'signature' => $sigData['sig']
										);

					$charID = 0;
					if( $notifier['scope'] == 'personal' )
					{
						$charID = Auth::$session->charID;
					}

					Notification::create(Auth::$session->groupID, $charID, $notifier['type'], $eventData);
				}
			}
		}
	}

	public function action_mass_add()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		$this->response->headers('Content-Type','application/json');
		$this->response->headers('Cache-Control','no-cache, must-revalidate');

		if(	 !$this->siggyAccessGranted() )
		{
			$this->response->body(json_encode(['error' => 1, 'errorMsg' => 'Invalid auth']));
			return;
		}

		//load settings to trigger localization
		$this->loadSettings();

		if( isset($_POST['systemID']) && isset($_POST['blob']) && !empty($_POST['blob']) )
		{
			$sigs = miscUtils::parseIngameSigExport( $_POST['blob'] );

			$systemID = intval($_POST['systemID']);

			$addedSigs = array();


			$deleteNonExisting = isset($_POST['delete_nonexistent_sigs']) ? (int)$_POST['delete_nonexistent_sigs'] : 0;
			if( $deleteNonExisting )
			{
				$sigList = array();
				foreach( $sigs as $sig )
				{
					$sigList[] = Database::instance()->escape($sig['sig']);
				}

				DB::query(Database::DELETE, "DELETE FROM systemsigs
													WHERE groupID=:groupID
													AND systemID=:id
													AND sig NOT IN(".implode($sigList,',').")")
								->param(':groupID', Auth::$session->groupID)
								->param(':id', $systemID)
								->execute();
			}


			if( count($sigs) > 0 && count($sigs) < 200 )	//200 is safety limit to prevent attacks, no system should have this many sigs
			{
				$doingUpdate = FALSE;
				foreach( $sigs as $sig )
				{
					$sigData = DB::query(Database::SELECT, "SELECT sigID,sig, type, siteID, description, created
															FROM systemsigs
															WHERE systemID=:id
																AND groupID=:group
																 AND sig=:sig")
												->param(':id', $systemID)
												->param(':group',Auth::$session->groupID)
												->param(':sig', $sig['sig'] )
												->execute()
												->current();

					if( isset($sigData['sigID']) )
					{
						if(  $sig['type'] != 'none' || $sig['siteID'] != 0 )
						{
							$doingUpdate = TRUE;
							$update = array(
											'updated' => time(),
											'siteID' => ( $sig['siteID'] != 0 ) ? $sig['siteID'] : $sigData['siteID'],
											'type' => $sig['type'],
											'lastUpdater' => Auth::$session->charName
											);

							DB::update('systemsigs')->set( $update )->where('sigID', '=', $sigData['sigID'])->execute();
						}
					}
					else
					{
						$insert = array();
						$insert['systemID'] = intval($systemID);
						$insert['sig'] = strtoupper($sig['sig']);
						$insert['description'] = "";
						$insert['created'] = time();
						$insert['siteID'] = intval($sig['siteID']);
						$insert['type'] = $sig['type'];
						$insert['groupID'] = Auth::$session->groupID;
						$insert['sigSize'] = "";	//need to return this value for JS to fail gracefully
						$insert['creator'] = Auth::$session->charName;

						$sigID = DB::insert('systemsigs', array_keys($insert) )->values(array_values($insert))->execute();

						$insert['sigID'] = $sigID[0];

						$addedSigs[ $sigID[0] ] = $insert;

						if( $insert['type'] != 'none' )
						{
							miscUtils::increment_stat('adds', Auth::$session->accessData);
						}
					}
				}

				if( $doingUpdate )
				{
					$this->chainmap->update_system($systemID, array('lastUpdate' => time(),'lastActive' => time() ) );
				}

				$this->response->body(json_encode($addedSigs));
			}
		}
	}

	public function action_edit()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		$this->response->headers('Content-Type','application/json');
		$this->response->headers('Cache-Control','no-cache, must-revalidate');

		$sigData = json_decode($this->request->body(), true);

		if( !empty($sigData) && isset($sigData['sigID']) )
		{
			$update['sig'] = strtoupper($sigData['sig']);
			$update['description'] = $sigData['desc'];
			$update['updated'] = time();
			$update['siteID'] = isset($sigData['siteID']) ? intval($sigData['siteID']) : 0;
			$update['type'] = $sigData['type'];

			if( Auth::$session->accessData['showSigSizeCol'] )
			{
				$update['sigSize'] = ( is_numeric( $sigData['sigSize'] ) ? $sigData['sigSize'] : ''  );
			}

			$update['lastUpdater'] = Auth::$session->charName;

			$id = intval($sigData['sigID']);

			DB::update('systemsigs')
				->set( $update )
				->where('groupID', '=', Auth::$session->groupID)
				->where('sigID', '=', $id)
				->execute();

			$this->chainmap->update_system($sigData['systemID'], array('lastUpdate' => time(), 'lastActive' => time() ) );

			if(!empty($sigData['chainmap_wormhole']))
			{
				if($sigData['chainmap_wormhole']['hash'] == 'none')
				{
	                DB::query(Database::DELETE, 'DELETE FROM wormhole_signatures WHERE `chainmap_id`=:chainMapID AND `signature_id` = :sigID')
								->param(':chainMapID', $sigData['chainmap_wormhole']['chainmap_id'])
								->param(':sigID', $id)
								->execute();
				}
				else
				{
	                DB::query(Database::INSERT, 'REPLACE INTO wormhole_signatures (`wormhole_hash`, `chainmap_id`,`signature_id`)
					VALUES(:hash, :chainMapID, :sigID)')
								->param(':hash', $sigData['chainmap_wormhole']['hash'] )
								->param(':chainMapID', $sigData['chainmap_wormhole']['chainmap_id'])
								->param(':sigID', $id)
								->execute();
				}
			}

			miscUtils::increment_stat('updates', Auth::$session->accessData);

			$this->response->body(json_encode('1'));
		}
	}

	public function action_remove()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		$this->response->headers('Content-Type','application/json');
		$this->response->headers('Cache-Control','no-cache, must-revalidate');

		if( isset($_POST['sigID']) )
		{
			$id = intval($_POST['sigID']);
			$sigData = DB::query(Database::SELECT, 'SELECT *,ss.name as systemName FROM	 systemsigs s
													INNER JOIN solarsystems ss ON ss.id = s.systemID
													WHERE s.sigID=:sigID AND s.groupID=:groupID')
									->param(':groupID', Auth::$session->groupID)
									->param(':sigID', $id)
									->execute()
									->current();

			DB::delete('systemsigs')
			->where('groupID', '=', Auth::$session->groupID)
			->where('sigID', '=', $id)
			->execute();

			$this->chainmap->update_system($_POST['systemID'], array('lastUpdate' => time() ));

			$message = Auth::$session->charName.' deleted sig "'.$sigData['sig'].'" from system '.$sigData['systemName'];;
			if( $sigData['type'] != 'none' )
			{
				$message .= '" which was of type '.strtoupper($sigData['type']);
			}

			groupUtils::log_action(Auth::$session->groupID, 'delsig', $message);
			$this->response->body(json_encode('1'));
		}
	}

	public function action_scanned_systems()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		$this->response->headers('Content-Type','application/json');
		$this->response->headers('Cache-Control','no-cache, must-revalidate');

		$data = DB::query(Database::SELECT, "SELECT ss.name as system_name, ss.id,
													r.regionName as region_name,
													r.regionID as region_id,
													c.constellationID as constellation_id,
													c.constellationName as constellation_name,
													(SELECT created FROM systemsigs
													WHERE systemID = ss.id AND groupID=:groupID
													ORDER BY created DESC
													LIMIT 1)
													as last_scan
												FROM solarsystems ss
												INNER JOIN regions r ON(r.regionID=ss.region)
												INNER JOIN constellations c ON(c.constellationID=ss.constellation)
												WHERE ss.id IN (
													SELECT s.systemID FROM	 systemsigs s
													WHERE s.sig !='POS' AND s.groupID=:groupID
													GROUP BY s.systemID
												)")
								->param(':groupID', Auth::$session->groupID)
								->execute()
								->as_array();

		$this->response->body(json_encode($data));
	}
}
